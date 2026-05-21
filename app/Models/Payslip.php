<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','payroll_item_id','employee_id','slip_number','slip_date','pdf_path','sent_email_at','sent_whatsapp_at','opened_at','downloaded_at','password_hint'];
    protected $casts = ['slip_date'=>'date','sent_email_at'=>'datetime','sent_whatsapp_at'=>'datetime','opened_at'=>'datetime','downloaded_at'=>'datetime'];
}
