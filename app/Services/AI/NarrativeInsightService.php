<?php

namespace App\Services\AI;

use App\Adapters\AdapterRegistry;
use App\Adapters\Contracts\LLMAdapterInterface;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

/**
 * AI Narrative Insight — generate executive narrative laporan dari data analytics.
 *
 * Pattern:
 *   1. Aggregate metric (turnover, payroll, attendance, retention) per dept/period
 *   2. Format ke struktur data ringkas
 *   3. Send ke LLM dengan prompt analyst untuk dapat narrative bahasa Indonesia
 *   4. Return: ringkasan eksekutif + 3-5 actionable insight + warning flags
 */
class NarrativeInsightService
{
    public function __construct(private AdapterRegistry $registry) {}

    public function monthlyHrSummary(int $tenantId, int $companyId): array
    {
        $metrics = $this->collectMonthlyMetrics($tenantId, $companyId);

        $adapter = $this->resolveAdapter($tenantId);
        if (!$adapter) {
            return ['error' => 'Belum ada LLM provider. Konfigurasi di Settings > Integrations.'];
        }

        $result = $adapter->complete([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => "Metric HR bulan ini:\n" . json_encode($metrics, JSON_PRETTY_PRINT)],
        ]);

        return [
            'narrative' => $result['content'],
            'raw_metrics' => $metrics,
            'usage' => $result['usage'] ?? null,
        ];
    }

    public function turnoverRiskForEmployee(Employee $employee): array
    {
        $signals = $this->collectTurnoverSignals($employee);

        $adapter = $this->resolveAdapter($employee->tenant_id);
        if (!$adapter) return ['error' => 'No LLM provider configured'];

        $result = $adapter->complete([
            ['role' => 'system', 'content' => 'Kamu adalah HR analyst. Score risk turnover karyawan ini dari 0-100 dan jelaskan 3 faktor utama. Output JSON: {"risk_score":int, "factors":["...","..."], "recommendation":"..."}'],
            ['role' => 'user', 'content' => json_encode($signals)],
        ]);

        $parsed = json_decode(preg_replace('/```(json)?/', '', $result['content']), true) ?: [];
        return [
            'employee_id' => $employee->id,
            'risk_score' => $parsed['risk_score'] ?? null,
            'factors' => $parsed['factors'] ?? [],
            'recommendation' => $parsed['recommendation'] ?? null,
        ];
    }

    private function collectMonthlyMetrics(int $tenantId, int $companyId): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return [
            'period' => $start->format('Y-m'),
            'headcount' => DB::table('employees')->where('tenant_id', $tenantId)->where('company_id', $companyId)->whereNull('deleted_at')->whereIn('employment_status', ['active','probation'])->count(),
            'new_hires' => DB::table('employees')->where('tenant_id', $tenantId)->where('company_id', $companyId)->whereBetween('join_date', [$start, $end])->count(),
            'resignations' => DB::table('employees')->where('tenant_id', $tenantId)->where('company_id', $companyId)->whereBetween('last_working_date', [$start, $end])->count(),
            'attendance_avg_late_minutes' => (int) DB::table('attendance_logs')->where('tenant_id', $tenantId)->whereBetween('date', [$start, $end])->avg('check_in_late_minutes'),
            'overtime_hours_total' => (float) DB::table('overtime_requests')->where('tenant_id', $tenantId)->where('status', 'completed')->whereBetween('date', [$start, $end])->sum('actual_hours'),
            'leave_requests_count' => DB::table('leave_requests')->where('tenant_id', $tenantId)->whereBetween('start_date', [$start, $end])->count(),
            'payroll_total_gross' => (float) DB::table('payroll_runs')->where('tenant_id', $tenantId)->whereBetween('completed_at', [$start, $end])->sum('total_gross'),
            'payroll_total_tax' => (float) DB::table('payroll_runs')->where('tenant_id', $tenantId)->whereBetween('completed_at', [$start, $end])->sum('total_tax'),
            'department_breakdown' => DB::table('employees as e')
                ->leftJoin('departments as d', 'd.id', '=', 'e.department_id')
                ->where('e.tenant_id', $tenantId)
                ->where('e.company_id', $companyId)
                ->whereIn('e.employment_status', ['active','probation'])
                ->groupBy('d.name')
                ->selectRaw('COALESCE(d.name, "Unassigned") as dept, COUNT(*) as count')
                ->pluck('count', 'dept')
                ->all(),
        ];
    }

    private function collectTurnoverSignals(Employee $employee): array
    {
        $sixMonthsAgo = now()->subMonths(6);
        return [
            'employee_no' => $employee->employee_no,
            'tenure_months' => $employee->join_date ? $employee->join_date->diffInMonths(now()) : null,
            'employment_status' => $employee->employment_status,
            'recent_leaves' => DB::table('leave_requests')->where('employee_id', $employee->id)->where('start_date', '>=', $sixMonthsAgo)->count(),
            'late_count_6mo' => DB::table('attendance_logs')->where('employee_id', $employee->id)->where('check_in_is_late', true)->where('date', '>=', $sixMonthsAgo)->count(),
            'absent_count_6mo' => DB::table('attendance_logs')->where('employee_id', $employee->id)->where('status', 'absent')->where('date', '>=', $sixMonthsAgo)->count(),
            'overtime_hours_6mo' => (float) DB::table('overtime_requests')->where('employee_id', $employee->id)->where('date', '>=', $sixMonthsAgo)->sum('actual_hours'),
            'last_promotion' => DB::table('position_history')->where('employee_id', $employee->id)->where('reason', 'promotion')->latest('effective_from')->value('effective_from'),
            'salary_now' => DB::table('contracts')->where('employee_id', $employee->id)->where('status', 'active')->value('salary_base'),
        ];
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah HR analyst senior untuk perusahaan Indonesia. Diberi metric bulanan, hasilkan narrative laporan executive 4-6 paragraf BAHASA INDONESIA dengan struktur:

1. *Ringkasan Eksekutif* (1 paragraf): kondisi headcount, finansial, & operasional.
2. *Highlight Positif* (1 paragraf): apa yang berjalan baik.
3. *Concern & Anomali* (1 paragraf): apa yang perlu perhatian, dengan angka spesifik.
4. *Insight & Rekomendasi* (3-5 bullet): tindakan konkret untuk HRD bulan depan.
5. *Warning Flags* (jika ada): metric yang sudah masuk zona merah.

Gunakan tone profesional, langsung ke point, fokus pada actionable insight. Hindari jargon umum.
PROMPT;
    }

    private function resolveAdapter(int $tenantId): ?LLMAdapterInterface
    {
        $adapter = $this->registry->resolveDefaultForDomain($tenantId, 'llm');
        return $adapter instanceof LLMAdapterInterface ? $adapter : null;
    }
}
