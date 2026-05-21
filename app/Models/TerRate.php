<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerRate extends Model
{
    protected $table = 'ter_rates';
    protected $fillable = ['year','category','bracket_from','bracket_to','rate_percent'];
    protected $casts = ['bracket_from'=>'decimal:2','bracket_to'=>'decimal:2','rate_percent'=>'decimal:4'];
}
