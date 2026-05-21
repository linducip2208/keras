<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AdapterConfig extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','domain','name','format','base_url','api_key_encrypted','api_secret_encrypted','extra_headers','extra_config','is_active','is_test_mode','last_tested_at','last_test_status'];
    protected $casts = [
        'api_key_encrypted'=>'encrypted','api_secret_encrypted'=>'encrypted',
        'extra_headers'=>'array','extra_config'=>'array',
        'is_active'=>'boolean','is_test_mode'=>'boolean',
        'last_tested_at'=>'datetime',
    ];
}
