<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRequest extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','date','planned_start','planned_end','actual_start','actual_end','planned_hours','actual_hours','reason','attachment_path','overtime_type','status','amount_calculated','use_compensatory_leave'];
    protected $casts = ['date'=>'date','actual_start'=>'datetime','actual_end'=>'datetime','planned_hours'=>'decimal:2','actual_hours'=>'decimal:2','amount_calculated'=>'decimal:2','use_compensatory_leave'=>'boolean'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
