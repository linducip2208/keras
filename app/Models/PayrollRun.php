<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRun extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_period_id','run_type','status','total_employees','total_gross','total_net','total_tax','total_bpjs_employer','total_bpjs_employee','started_at','completed_at','approved_by_user_id','approved_at','created_by_user_id'];
    protected $casts = ['started_at'=>'datetime','completed_at'=>'datetime','approved_at'=>'datetime','total_gross'=>'decimal:2','total_net'=>'decimal:2','total_tax'=>'decimal:2','total_bpjs_employer'=>'decimal:2','total_bpjs_employee'=>'decimal:2'];

    public function period() { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function items() { return $this->hasMany(PayrollItem::class); }
}
