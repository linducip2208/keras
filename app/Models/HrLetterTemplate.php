<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrLetterTemplate extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','letter_type','name','body_template','variables','default_signer_role','is_active'];
    protected $casts = ['variables'=>'array','is_active'=>'boolean'];
}
