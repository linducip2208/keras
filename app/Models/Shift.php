<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','code','name','start_time','end_time','break_minutes','working_hours','crosses_midnight','allowed_late_minutes','allowed_early_leave_minutes','color','is_off_day'];
    protected $casts = ['crosses_midnight'=>'boolean','is_off_day'=>'boolean','working_hours'=>'decimal:2'];
}
