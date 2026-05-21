<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExitRequest extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','exit_type','notice_date','last_working_date','reason','reason_category','voluntary','eligible_for_rehire','status','paklaring_letter_id'];
    protected $casts = ['notice_date'=>'date','last_working_date'=>'date','voluntary'=>'boolean','eligible_for_rehire'=>'boolean'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
