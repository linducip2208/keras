<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'legal_name', 'npwp',
        'npp_bpjs_kesehatan', 'npp_bpjs_naker', 'address', 'phone', 'email',
        'logo_path', 'parent_company_id', 'country', 'timezone', 'currency',
        'fiscal_year_start_month', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function branches() { return $this->hasMany(Branch::class); }
    public function departments() { return $this->hasMany(Department::class); }
    public function employees() { return $this->hasMany(Employee::class); }
}
