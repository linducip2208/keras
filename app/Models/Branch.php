<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'company_id', 'code', 'name', 'address', 'phone',
        'latitude', 'longitude', 'geofence_radius_meters', 'working_hours',
        'umk_amount', 'is_default',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function company() { return $this->belongsTo(Company::class); }
}
