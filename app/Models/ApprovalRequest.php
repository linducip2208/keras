<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','workflow_id','requestable_type','requestable_id','requester_employee_id','status','current_step_order','total_steps','submitted_at','completed_at'];
    protected $casts = ['submitted_at'=>'datetime','completed_at'=>'datetime'];

    public function workflow() { return $this->belongsTo(ApprovalWorkflow::class); }
    public function steps() { return $this->hasMany(ApprovalRequestStep::class)->orderBy('step_order'); }
    public function requestable() { return $this->morphTo(); }
}
