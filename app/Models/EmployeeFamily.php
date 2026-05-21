<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
    use BelongsToTenant;
    protected $table = 'employee_families';
    protected $fillable = ['tenant_id','employee_id','relation','name','gender','date_of_birth','nik','is_bpjs_dependent','is_ptkp_dependent'];
    protected $casts = ['date_of_birth'=>'date','nik'=>'encrypted','is_bpjs_dependent'=>'boolean','is_ptkp_dependent'=>'boolean'];
}
