<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequestStep extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','approval_request_id','step_order','approver_employee_id','action','comment','attachment_path','acted_at','due_at','delegated_to_employee_id'];
    protected $casts = ['acted_at'=>'datetime','due_at'=>'datetime'];

    public function approver() { return $this->belongsTo(Employee::class, 'approver_employee_id'); }
    public function approvalRequest() { return $this->belongsTo(ApprovalRequest::class); }
}
