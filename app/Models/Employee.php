<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'user_id', 'company_id', 'branch_id', 'department_id',
        'position_id', 'job_title_id', 'job_level_id',
        'employee_no', 'nik', 'npwp', 'full_name', 'nickname', 'gender',
        'place_of_birth', 'date_of_birth', 'religion', 'marital_status',
        'ptkp_status', 'blood_type', 'nationality',
        'ktp_address', 'current_address', 'postal_code',
        'phone', 'phone_alt', 'personal_email', 'employee_email',
        'join_date', 'probation_end_date', 'employment_status', 'employment_type',
        'last_working_date', 'photo_path', 'signature_path', 'qr_badge_code',
        'manager_employee_id', 'dotted_line_manager_id', 'custom_fields',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'probation_end_date' => 'date',
        'last_working_date' => 'date',
        'custom_fields' => 'array',
        'nik' => 'encrypted',
        'npwp' => 'encrypted',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function position() { return $this->belongsTo(Position::class); }
    public function jobTitle() { return $this->belongsTo(JobTitle::class); }
    public function jobLevel() { return $this->belongsTo(JobLevel::class); }
    public function manager() { return $this->belongsTo(Employee::class, 'manager_employee_id'); }
    public function subordinates() { return $this->hasMany(Employee::class, 'manager_employee_id'); }
    public function families() { return $this->hasMany(EmployeeFamily::class); }
    public function bankAccounts() { return $this->hasMany(EmployeeBankAccount::class); }
    public function documents() { return $this->hasMany(EmployeeDocument::class); }
    public function contracts() { return $this->hasMany(Contract::class); }
}
