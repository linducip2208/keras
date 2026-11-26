<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post-auth tenant binding untuk API.
 *
 * Dipasang SETELAH auth:sanctum sehingga tenant selalu berasal dari
 * user yang terautentikasi — bukan dari header/domain yang dikontrol klien.
 * Fail-closed: user non-super-admin tanpa tenant yang valid ditolak 403.
 */
class ResolveApiTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->is_super_admin) {
            return $next($request);
        }

        if (!$user->tenant_id) {
            abort(403, 'Akun tidak terhubung ke tenant manapun.');
        }

        $tenant = Tenant::where('id', $user->tenant_id)->first();
        if (!$tenant || $tenant->status === 'suspended') {
            abort(403, 'Tenant tidak aktif.');
        }

        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}
