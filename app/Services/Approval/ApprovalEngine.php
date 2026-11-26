<?php

namespace App\Services\Approval;

use App\Models\ApprovalRequest;
use App\Models\ApprovalRequestStep;
use App\Models\ApprovalWorkflow;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Approval Workflow Engine — implements F12 (docs/06-BUSINESS-FLOWS.md).
 * Submit, approve, reject, delegate, escalate.
 */
class ApprovalEngine
{
    /**
     * Submit a request for approval. Resolves workflow, creates approval_request + first step.
     */
    public function submit(string $applicableType, Model $requestable, Employee $requester): ApprovalRequest
    {
        return DB::transaction(function () use ($applicableType, $requestable, $requester) {
            $workflow = ApprovalWorkflow::where('tenant_id', $requester->tenant_id)
                ->where('applies_to', $applicableType)
                ->where('is_active', true)
                ->orderByDesc('priority')
                ->first();

            // Default workflow: manager only
            if (!$workflow) {
                $workflow = $this->ensureDefaultWorkflow($applicableType, $requester->tenant_id);
            }

            $steps = $workflow->steps;
            $request = ApprovalRequest::create([
                'tenant_id' => $requester->tenant_id,
                'workflow_id' => $workflow->id,
                'requestable_type' => get_class($requestable),
                'requestable_id' => $requestable->id,
                'requester_employee_id' => $requester->id,
                'status' => 'pending',
                'current_step_order' => 1,
                'total_steps' => $steps->count(),
                'submitted_at' => now(),
            ]);

            $firstStep = $steps->firstWhere('order', 1) ?? $steps->first();
            if ($firstStep) {
                $this->createStepRow($request, $firstStep, $requester);
            }

            return $request;
        });
    }

    public function approve(ApprovalRequestStep $step, ?string $comment = null): void
    {
        DB::transaction(function () use ($step, $comment) {
            // Guard idempotensi: muat ulang dan tolak jika step sudah diproses
            // (mencegah double-approve dari retry/concurrent request).
            $step->refresh();
            if ($step->action !== 'pending') {
                return;
            }

            $step->update([
                'action' => 'approved',
                'comment' => $comment,
                'acted_at' => now(),
            ]);

            $request = $step->approvalRequest ?? ApprovalRequest::find($step->approval_request_id);
            $workflow = ApprovalWorkflow::find($request->workflow_id);
            $nextStepDef = $workflow->steps()->where('order', $step->step_order + 1)->first();

            if ($nextStepDef) {
                $request->update(['current_step_order' => $nextStepDef->order]);
                $requester = Employee::find($request->requester_employee_id);
                $this->createStepRow($request, $nextStepDef, $requester);
            } else {
                $request->update(['status' => 'approved', 'completed_at' => now()]);
                $this->onRequestApproved($request);
            }
        });
    }

    public function reject(ApprovalRequestStep $step, string $comment): void
    {
        DB::transaction(function () use ($step, $comment) {
            $step->refresh();
            if ($step->action !== 'pending') {
                return;
            }

            $step->update([
                'action' => 'rejected',
                'comment' => $comment,
                'acted_at' => now(),
            ]);

            $request = $step->approvalRequest ?? ApprovalRequest::find($step->approval_request_id);
            $request->update(['status' => 'rejected', 'completed_at' => now()]);
            $this->onRequestRejected($request, $comment);
        });
    }

    public function delegate(ApprovalRequestStep $step, Employee $newApprover): void
    {
        $step->update([
            'action' => 'delegated',
            'delegated_to_employee_id' => $newApprover->id,
            'acted_at' => now(),
        ]);

        // Create new pending step for the delegate
        $request = $step->approvalRequest ?? ApprovalRequest::find($step->approval_request_id);
        ApprovalRequestStep::create([
            'tenant_id' => $request->tenant_id,
            'approval_request_id' => $request->id,
            'step_order' => $step->step_order,
            'approver_employee_id' => $newApprover->id,
            'action' => 'pending',
        ]);
    }

    private function createStepRow(ApprovalRequest $request, $stepDef, Employee $requester): void
    {
        $approverId = $this->resolveApprover($stepDef, $requester);
        ApprovalRequestStep::create([
            'tenant_id' => $request->tenant_id,
            'approval_request_id' => $request->id,
            'step_order' => $stepDef->order,
            'approver_employee_id' => $approverId,
            'action' => 'pending',
            'due_at' => $stepDef->timeout_hours ? now()->addHours($stepDef->timeout_hours) : null,
        ]);
    }

    private function resolveApprover($stepDef, Employee $requester): ?int
    {
        return match ($stepDef->approver_type) {
            'manager' => $requester->manager_employee_id,
            'manager_of_manager' => optional($requester->manager)->manager_employee_id,
            'employee', 'role', 'position', 'hr', 'finance', 'custom' => $stepDef->approver_id,
            default => $stepDef->approver_id,
        };
    }

    private function ensureDefaultWorkflow(string $applicableType, int $tenantId): ApprovalWorkflow
    {
        $workflow = ApprovalWorkflow::create([
            'tenant_id' => $tenantId,
            'slug' => "default-{$applicableType}",
            'name' => "Default {$applicableType} approval",
            'applies_to' => $applicableType,
            'is_active' => true,
            'priority' => 1,
        ]);

        $workflow->steps()->create([
            'tenant_id' => $tenantId,
            'workflow_id' => $workflow->id,
            'order' => 1,
            'name' => 'Manager Approval',
            'approver_type' => 'manager',
            'approval_mode' => 'single',
        ]);

        return $workflow->fresh('steps');
    }

    private function onRequestApproved(ApprovalRequest $request): void
    {
        $cls = $request->requestable_type;
        $r = $cls::find($request->requestable_id);
        if (!$r) return;

        // Update underlying request status
        if (in_array('status', $r->getFillable() ?? [])) {
            $r->update(['status' => 'approved']);
        }

        // Side effects per type
        if ($cls === \App\Models\LeaveRequest::class) {
            // Move pending to used. days_count di-cast float agar aman
            // dari interpolasi SQL (nilai selalu numerik dari DB).
            $days = (float) $r->days_count;
            \App\Models\LeaveBalance::where('employee_id', $r->employee_id)
                ->where('leave_type_id', $r->leave_type_id)
                ->where('year', now()->year)
                ->update([
                    'pending' => DB::raw('GREATEST(pending - ' . $days . ', 0)'),
                    'used' => DB::raw('used + ' . $days),
                ]);
        }
    }

    private function onRequestRejected(ApprovalRequest $request, string $reason): void
    {
        $cls = $request->requestable_type;
        $r = $cls::find($request->requestable_id);
        if (!$r) return;

        if (in_array('status', $r->getFillable() ?? [])) {
            $r->update(['status' => 'rejected']);
        }

        // Restore leave balance pending
        if ($cls === \App\Models\LeaveRequest::class) {
            $days = (float) $r->days_count;
            \App\Models\LeaveBalance::where('employee_id', $r->employee_id)
                ->where('leave_type_id', $r->leave_type_id)
                ->where('year', now()->year)
                ->update([
                    'pending' => DB::raw('GREATEST(pending - ' . $days . ', 0)'),
                ]);
        }
    }
}
