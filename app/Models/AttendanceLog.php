<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id','employee_id','date','shift_id',
        'check_in_at','check_in_latitude','check_in_longitude','check_in_address',
        'check_in_photo_path','check_in_method','check_in_branch_id',
        'check_in_is_late','check_in_late_minutes','check_in_is_mock_gps',
        'check_out_at','check_out_latitude','check_out_longitude','check_out_address',
        'check_out_photo_path','check_out_method','check_out_branch_id',
        'check_out_is_early','check_out_early_minutes','check_out_is_mock_gps',
        'worked_minutes','overtime_minutes','status','notes',
    ];
    protected $casts = [
        'date'=>'date','check_in_at'=>'datetime','check_out_at'=>'datetime',
        'check_in_is_late'=>'boolean','check_in_is_mock_gps'=>'boolean',
        'check_out_is_early'=>'boolean','check_out_is_mock_gps'=>'boolean',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function shift() { return $this->belongsTo(Shift::class); }
}
