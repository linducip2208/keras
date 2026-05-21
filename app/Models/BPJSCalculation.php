<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class BPJSCalculation extends Model
{
    use BelongsToTenant;
    protected $table = 'bpjs_calculations';
    protected $fillable = ['tenant_id','payroll_item_id','employee_id','program','basis_amount','employer_amount','employee_amount'];
    protected $casts = ['basis_amount'=>'decimal:2','employer_amount'=>'decimal:2','employee_amount'=>'decimal:2'];
}
