<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollComponent extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','code','name','type','calculation','formula','default_amount','is_taxable','is_bpjs_basis','is_thr_basis','is_pension_basis','display_order','is_active'];
    protected $casts = ['is_taxable'=>'boolean','is_bpjs_basis'=>'boolean','is_thr_basis'=>'boolean','is_pension_basis'=>'boolean','is_active'=>'boolean','default_amount'=>'decimal:2'];
}
