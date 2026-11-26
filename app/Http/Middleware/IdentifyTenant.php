<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant) {
            app()->instance('currentTenant', $tenant);
            config(['app.timezone' => $tenant->timezone]);
            config(['app.locale' => $tenant->locale]);
        }

        return $next($request);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        try {
            // 1. Request terautentikasi selalu menang: ikut tenant milik user.
            //    Ini menutup celah X-Tenant-ID spoofing (CRITICAL-2).
            $user = auth()->user();
            if ($user && $user->tenant_id) {
                return Tenant::where('id', $user->tenant_id)->where('status', '!=', 'suspended')->first();
            }

            // 2. Pre-auth / publik: subdomain atau custom domain.
            //    TIDAK ADA lagi fallback ke tenant 'acme' — tanpa tenant yang
            //    cocok, request berjalan sebagai konteks publik tanpa scope.
            $host = $request->getHost();
            $baseDomain = config('app.base_domain', 'hris.test');

            if ($host === $baseDomain) {
                return null;
            }

            if (str_ends_with($host, '.' . $baseDomain)) {
                $subdomain = str_replace('.' . $baseDomain, '', $host);
                return Tenant::where('subdomain', $subdomain)->first();
            }

            return Tenant::where('custom_domain', $host)->first();
        } catch (\Throwable $e) {
            // DB error: untuk request terautentikasi kita FAIL-CLOSED di
            // ResolveApiTenant / lapisan otorisasi, bukan di sini agar
            // halaman publik tidak ikut mati. Kegagalan dicatat.
            \Log::warning('IdentifyTenant resolve failed: ' . $e->getMessage());
            return null;
        }
    }
}
