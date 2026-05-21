<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AIScreening extends Model
{
    use BelongsToTenant;
    protected $table = 'ai_screenings';
    protected $fillable = [
        'tenant_id','application_id','adapter_config_id','adapter_name','model_used',
        'score','summary','strengths','weaknesses','recommendation','reasoning',
        'raw_response','usage','cost','status','error_message','triggered_by_user_id',
    ];
    protected $casts = [
        'strengths' => 'array', 'weaknesses' => 'array', 'raw_response' => 'array',
        'usage' => 'array', 'score' => 'decimal:2', 'cost' => 'decimal:4',
    ];

    public function application() { return $this->belongsTo(Application::class); }
    public function adapterConfig() { return $this->belongsTo(AdapterConfig::class); }
}
