<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','category_id','code','name','brand','model','serial_number','color','purchase_date','purchase_amount','supplier','warranty_until','current_value','condition','location','status','photo_path','notes'];
    protected $casts = ['purchase_date'=>'date','warranty_until'=>'date','purchase_amount'=>'decimal:2','current_value'=>'decimal:2'];

    public function category() { return $this->belongsTo(AssetCategory::class, 'category_id'); }
}
