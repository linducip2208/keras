<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflowStep extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','workflow_id','order','name','approver_type','approver_id','approval_mode','timeout_hours','escalation_step_id','is_optional'];
    protected $casts = ['is_optional'=>'boolean'];
}
