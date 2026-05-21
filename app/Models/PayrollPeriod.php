<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','company_id','name','period_type','start_date','end_date','cutoff_attendance_date','payroll_date','status'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','cutoff_attendance_date'=>'date','payroll_date'=>'date'];

    public function company() { return $this->belongsTo(Company::class); }
    public function runs() { return $this->hasMany(PayrollRun::class); }
}
