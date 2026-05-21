<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'company_id', 'department_id', 'job_title_id', 'job_level_id',
        'code', 'name', 'headcount_target',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function jobTitle() { return $this->belongsTo(JobTitle::class); }
    public function jobLevel() { return $this->belongsTo(JobLevel::class); }
}
