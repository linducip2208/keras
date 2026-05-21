<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReimbursementRequest extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','request_no','total_amount','currency','purpose','business_trip_id','status','disbursement_method','disbursement_run_id','submitted_at','approved_at','paid_at'];
    protected $casts = ['submitted_at'=>'datetime','approved_at'=>'datetime','paid_at'=>'datetime','total_amount'=>'decimal:2'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function items() { return $this->hasMany(ReimbursementItem::class, 'request_id'); }
}
