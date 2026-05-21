<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','category_id','name','file_path','file_size','mime_type','expires_at','uploaded_by_user_id','metadata'];
    protected $casts = ['expires_at'=>'date','metadata'=>'array'];
}
