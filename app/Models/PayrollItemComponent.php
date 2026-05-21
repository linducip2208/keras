<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PayrollItemComponent extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_item_id','payroll_component_id','amount','quantity','unit_price','formula_used','calculation_breakdown','is_taxable','is_bpjs_basis'];
    protected $casts = ['calculation_breakdown'=>'array','is_taxable'=>'boolean','is_bpjs_basis'=>'boolean','amount'=>'decimal:2','quantity'=>'decimal:2','unit_price'=>'decimal:2'];

    public function component() { return $this->belongsTo(PayrollComponent::class, 'payroll_component_id'); }
}
