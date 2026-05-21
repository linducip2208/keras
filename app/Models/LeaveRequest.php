<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','leave_type_id','start_date','end_date','days_count','is_half_day','half_day_period','reason','attachment_path','contact_during_leave','delegate_employee_id','status','applied_at','canceled_at'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','days_count'=>'decimal:2','is_half_day'=>'boolean','applied_at'=>'datetime','canceled_at'=>'datetime'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function leaveType() { return $this->belongsTo(LeaveType::class); }
    public function delegate() { return $this->belongsTo(Employee::class, 'delegate_employee_id'); }
}
