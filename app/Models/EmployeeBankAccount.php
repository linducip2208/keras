<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EmployeeBankAccount extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','bank_name','account_number','account_holder_name','is_primary','allocation_percentage'];
    protected $casts = ['is_primary'=>'boolean','account_number'=>'encrypted','allocation_percentage'=>'decimal:2'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
