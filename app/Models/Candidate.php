<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','full_name','email','phone','linkedin_url','portfolio_url','location','current_position','years_experience','cv_path','photo_path','ai_summary','ai_score','talent_pool_tags','source','referred_by_employee_id'];
    protected $casts = ['talent_pool_tags'=>'array','ai_score'=>'decimal:2'];

    public function applications() { return $this->hasMany(Application::class); }
}
