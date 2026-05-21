<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrLetter extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','employee_id','letter_type','template_id','letter_no','letter_date','variables_data','body_rendered','file_path','status','created_by_user_id','signed_at','delivered_at','e_sign_provider','e_sign_reference_id'];
    protected $casts = ['letter_date'=>'date','variables_data'=>'array','signed_at'=>'datetime','delivered_at'=>'datetime'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function template() { return $this->belongsTo(HrLetterTemplate::class, 'template_id'); }
}
