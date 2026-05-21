<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalCycle extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','name','cycle_type','start_date','end_date','self_review_start','self_review_end','peer_review_start','peer_review_end','manager_review_start','manager_review_end','calibration_date','finalization_date','status'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','self_review_start'=>'date','self_review_end'=>'date','peer_review_start'=>'date','peer_review_end'=>'date','manager_review_start'=>'date','manager_review_end'=>'date','calibration_date'=>'date','finalization_date'=>'date'];
}
