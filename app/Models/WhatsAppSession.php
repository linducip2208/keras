<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    protected $table = 'whatsapp_sessions';
    protected $fillable = ['tenant_id','phone_number','employee_id','state','state_data','last_message_at','verified_at'];
    protected $casts = ['state_data' => 'array', 'last_message_at' => 'datetime', 'verified_at' => 'datetime'];

    public function employee() { return $this->belongsTo(Employee::class); }
}
