<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','name','depreciation_method','useful_life_years','gl_account_code'];
}
