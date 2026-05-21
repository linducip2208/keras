<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['tenant_id','title','body','attachment_paths','created_by_user_id','segment','publish_at','expire_at','is_pinned','priority','status'];
    protected $casts = ['attachment_paths'=>'array','segment'=>'array','publish_at'=>'datetime','expire_at'=>'datetime','is_pinned'=>'boolean'];
}
