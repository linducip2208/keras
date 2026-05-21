<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','leave_type_id','year','allocated','used','carried_over','pending','expires_at'];
    protected $casts = ['expires_at'=>'date','allocated'=>'decimal:2','used'=>'decimal:2','carried_over'=>'decimal:2','pending'=>'decimal:2'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function leaveType() { return $this->belongsTo(LeaveType::class); }
}
