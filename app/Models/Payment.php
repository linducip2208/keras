<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','invoice_id','adapter_config_id','reference','external_reference_id','amount','currency','payment_method','payment_channel','status','payment_url','qr_string','va_number','expires_at','paid_at','metadata','webhook_payload'];
    protected $casts = ['expires_at'=>'datetime','paid_at'=>'datetime','metadata'=>'array','webhook_payload'=>'array','amount'=>'decimal:2'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function adapter() { return $this->belongsTo(AdapterConfig::class, 'adapter_config_id'); }
}
