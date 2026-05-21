<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['slug','name','description','max_employees','price_monthly','price_annual','currency','features','is_active','display_order'];
    protected $casts = ['features' => 'array', 'is_active' => 'boolean', 'price_monthly' => 'decimal:2', 'price_annual' => 'decimal:2'];

    public function subscriptions() { return $this->hasMany(Subscription::class); }
}
