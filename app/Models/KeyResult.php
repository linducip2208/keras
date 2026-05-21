<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyResult extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','objective_id','title','description','metric_type','start_value','target_value','current_value','unit','progress_percent'];
    protected $casts = ['start_value'=>'decimal:2','target_value'=>'decimal:2','current_value'=>'decimal:2','progress_percent'=>'decimal:2'];
}
