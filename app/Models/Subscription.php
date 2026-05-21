<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','plan_id','status','current_period_start','current_period_end','canceled_at','trial_ends_at','billing_cycle','seats','external_subscription_id','auto_renew'];
    protected $casts = ['current_period_start'=>'date','current_period_end'=>'date','canceled_at'=>'datetime','trial_ends_at'=>'datetime','auto_renew'=>'boolean'];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
