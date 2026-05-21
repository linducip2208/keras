<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReimbursementCategory extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','name','slug','max_per_request','requires_receipt','requires_approval','gl_account_code','is_active'];
    protected $casts = ['requires_receipt'=>'boolean','requires_approval'=>'boolean','is_active'=>'boolean','max_per_request'=>'decimal:2'];
}
