<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','loan_no','loan_type','principal','interest_rate','tenor_months','installment_amount','total_payable','purpose','attachment_path','disbursement_run_id','status','disbursed_at','completed_at'];
    protected $casts = ['disbursed_at'=>'datetime','completed_at'=>'datetime','principal'=>'decimal:2','total_payable'=>'decimal:2','installment_amount'=>'decimal:2','interest_rate'=>'decimal:2'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
