<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ReimbursementItem extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id','request_id','category_id','item_date','description','amount','receipt_path','ocr_data','vendor_name'];
    protected $casts = ['item_date'=>'date','amount'=>'decimal:2','ocr_data'=>'array'];

    public function category() { return $this->belongsTo(ReimbursementCategory::class, 'category_id'); }
}
