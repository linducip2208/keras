<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtkpRule extends Model
{
    protected $table = 'ptkp_rules';
    protected $fillable = ['year','status','amount','description'];
    protected $casts = ['amount' => 'decimal:2'];
}
