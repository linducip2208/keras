<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TaxCalculation extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_item_id','employee_id','period_year','period_month','gross_basis','ptkp_amount','ter_category','ter_rate','pph_21_amount','method_used','calculation_breakdown'];
    protected $casts = ['calculation_breakdown'=>'array','gross_basis'=>'decimal:2','ptkp_amount'=>'decimal:2','ter_rate'=>'decimal:4','pph_21_amount'=>'decimal:2'];
}
