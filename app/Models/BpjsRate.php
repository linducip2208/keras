<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsRate extends Model
{
    protected $table = 'bpjs_rates';
    protected $fillable = ['year','effective_from','effective_to','program','employer_rate','employee_rate','salary_cap','notes'];
    protected $casts = ['effective_from'=>'date','effective_to'=>'date','employer_rate'=>'decimal:5','employee_rate'=>'decimal:5','salary_cap'=>'decimal:2'];
}
