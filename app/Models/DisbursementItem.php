<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DisbursementItem extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','disbursement_run_id','employee_id','bank_account_id','amount','bank_code','bank_name','account_number','account_holder','status','external_reference_id','error_message','processed_at'];
    protected $casts = ['processed_at'=>'datetime','amount'=>'decimal:2'];
}
