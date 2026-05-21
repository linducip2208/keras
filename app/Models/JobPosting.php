<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','company_id','department_id','position_id','title','slug','description','requirements','location','employment_type','salary_min','salary_max','salary_currency','status','published_at','closed_at','posted_by_user_id','external_links','public_token'];
    protected $casts = ['external_links'=>'array','published_at'=>'datetime','closed_at'=>'datetime','salary_min'=>'decimal:2','salary_max'=>'decimal:2'];

    public function applications() { return $this->hasMany(Application::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function position() { return $this->belongsTo(Position::class); }
}
