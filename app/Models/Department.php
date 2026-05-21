<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'company_id', 'parent_department_id', 'code', 'name',
        'cost_center_code', 'headcount_budget', 'head_employee_id',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function parent() { return $this->belongsTo(Department::class, 'parent_department_id'); }
    public function children() { return $this->hasMany(Department::class, 'parent_department_id'); }
}
