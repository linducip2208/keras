<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalWorkflow extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','slug','name','applies_to','conditions','is_active','priority'];
    protected $casts = ['conditions'=>'array','is_active'=>'boolean'];

    public function steps() { return $this->hasMany(ApprovalWorkflowStep::class, 'workflow_id')->orderBy('order'); }
}
