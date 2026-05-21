<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DisbursementRun extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_run_id','disbursement_method','adapter_slug','total_amount','total_count','status','file_path','executed_at','completed_at'];
    protected $casts = ['executed_at'=>'datetime','completed_at'=>'datetime','total_amount'=>'decimal:2'];

    public function items() { return $this->hasMany(DisbursementItem::class); }
    public function payrollRun() { return $this->belongsTo(PayrollRun::class); }
}
