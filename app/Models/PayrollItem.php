<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_run_id','employee_id','basic_salary','gross','total_earnings','total_deductions','total_benefits','pph_21','bpjs_employer','bpjs_employee','net_pay','working_days','present_days','absent_days','leave_days','overtime_hours','overtime_amount','prorate_days','prorate_factor','status','hold_reason','calculated_at'];
    protected $casts = ['calculated_at'=>'datetime','basic_salary'=>'decimal:2','gross'=>'decimal:2','total_earnings'=>'decimal:2','total_deductions'=>'decimal:2','net_pay'=>'decimal:2','pph_21'=>'decimal:2','prorate_factor'=>'decimal:4'];

    public function run() { return $this->belongsTo(PayrollRun::class, 'payroll_run_id'); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function components() { return $this->hasMany(PayrollItemComponent::class); }
}
