<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','contract_type','contract_no','template_id','start_date','end_date','probation_end_date','salary_base','salary_currency','job_title_id','position_id','department_id','branch_id','working_hours_per_week','status','parent_contract_id','file_path','signed_at','signed_by_employee_at','signed_by_company_at','e_sign_provider','e_sign_reference_id','notes'];
    protected $casts = ['start_date'=>'date','end_date'=>'date','probation_end_date'=>'date','signed_at'=>'datetime','signed_by_employee_at'=>'datetime','signed_by_company_at'=>'datetime','salary_base'=>'decimal:2'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
