<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            // Server-side enforcement: user non-super-admin yang login
            // SELALU menulis ke tenant miliknya — nilai tenant_id dari
            // request/form diabaikan (menutup mass-assignment lintas tenant).
            try {
                $user = auth()->user();
                if ($user && !$user->is_super_admin && $user->tenant_id) {
                    $model->tenant_id = $user->tenant_id;
                    return;
                }
            } catch (\Throwable) {
                // auth() tidak tersedia (CLI/queue) — lanjut ke fallback.
            }

            if (! $model->tenant_id && app()->bound('currentTenant')) {
                $model->tenant_id = app('currentTenant')?->id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
