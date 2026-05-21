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
            $headerTenant = $request->header('X-Tenant-ID');
            if ($headerTenant) {
                return Tenant::where('slug', $headerTenant)
                    ->orWhere('id', $headerTenant)
                    ->first();
            }

            $host = $request->getHost();
            $baseDomain = config('app.base_domain', 'hris.test');

            // hris.test (root) → default tenant
            if ($host === $baseDomain) {
                return Tenant::where('slug', 'acme')->first()
                    ?? Tenant::where('subdomain', 'default')->first();
            }

            // {subdomain}.hris.test
            if (str_ends_with($host, '.' . $baseDomain)) {
                $subdomain = str_replace('.' . $baseDomain, '', $host);
                return Tenant::where('subdomain', $subdomain)->first()
                    ?? Tenant::where('slug', 'acme')->first();
            }

            // Custom domain
            return Tenant::where('custom_domain', $host)->first()
                ?? Tenant::where('slug', 'acme')->first();
        } catch (\Throwable $e) {
            // DB unavailable / wrong connection — fail gracefully, jangan break aplikasi
            \Log::warning('IdentifyTenant resolve failed: ' . $e->getMessage());
            return null;
        }
    }
}
