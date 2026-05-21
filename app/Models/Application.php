<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','candidate_id','job_posting_id','current_stage_id','ai_match_score','cover_letter','salary_expectation','notice_period_days','status','hired_employee_id','rejected_reason','applied_at'];
    protected $casts = ['ai_match_score'=>'decimal:2','salary_expectation'=>'decimal:2','applied_at'=>'datetime'];

    public function candidate() { return $this->belongsTo(Candidate::class); }
    public function jobPosting() { return $this->belongsTo(JobPosting::class); }
    public function aiScreenings() { return $this->hasMany(AIScreening::class); }
}
