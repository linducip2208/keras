<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Objective extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','cycle_id','owner_employee_id','parent_objective_id','title','description','weight','progress_percent','is_public','status'];
    protected $casts = ['is_public'=>'boolean','weight'=>'decimal:2','progress_percent'=>'decimal:2'];

    public function cycle() { return $this->belongsTo(OkrCycle::class, 'cycle_id'); }
    public function owner() { return $this->belongsTo(Employee::class, 'owner_employee_id'); }
    public function keyResults() { return $this->hasMany(KeyResult::class); }
}
