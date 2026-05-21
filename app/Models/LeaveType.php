<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','code','name','days_per_year','is_paid','requires_attachment','requires_balance','gender_specific','max_consecutive_days','min_notice_days','can_be_half_day','can_carry_over','carry_over_max_days','carry_over_expires_in_months','deducts_from','is_active'];
    protected $casts = ['is_paid'=>'boolean','requires_attachment'=>'boolean','requires_balance'=>'boolean','can_be_half_day'=>'boolean','can_carry_over'=>'boolean','is_active'=>'boolean','deducts_from'=>'array','days_per_year'=>'decimal:2','carry_over_max_days'=>'decimal:2'];
}
