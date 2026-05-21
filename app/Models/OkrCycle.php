<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OkrCycle extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','company_id','name','start_date','end_date','status'];
    protected $casts = ['start_date'=>'date','end_date'=>'date'];

    public function objectives() { return $this->hasMany(Objective::class, 'cycle_id'); }
}
