<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','date','shift_id','is_off_day','source','notes'];
    protected $casts = ['date'=>'date','is_off_day'=>'boolean'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
}
