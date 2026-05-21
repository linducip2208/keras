<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','subscription_id','number','period_start','period_end','issue_date','due_date','subtotal','tax_amount','discount_amount','total_amount','currency','status','paid_at','paid_via','pdf_path','line_items'];
    protected $casts = ['period_start'=>'date','period_end'=>'date','issue_date'=>'date','due_date'=>'date','paid_at'=>'datetime','line_items'=>'array','subtotal'=>'decimal:2','total_amount'=>'decimal:2'];

    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
