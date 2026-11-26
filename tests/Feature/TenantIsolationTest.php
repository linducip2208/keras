<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tenant A tidak boleh membaca/mengubah data Tenant B via API.
 */
class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenant(string $slug): Tenant
    {
        return Tenant::create([
            'slug' => $slug, 'name' => $slug, 'subdomain' => $slug,
            'plan' => 'starter', 'status' => 'active',
            'country' => 'ID', 'timezone' => 'Asia/Jakarta', 'locale' => 'id',
            'currency' => 'IDR', 'billing_email' => "billing@{$slug}.test",
        ]);
    }

    public function test_leave_type_other_tenant_returns_404(): void
    {
        $tenantA = $this->makeTenant('tenant-a');
        $tenantB = $this->makeTenant('tenant-b');

        $userA = User::create([
            'tenant_id' => $tenantA->id, 'name' => 'User A',
            'email' => 'a@test.id', 'password' => bcrypt('password'), 'is_active' => true,
        ]);
        Employee::create([
            'tenant_id' => $tenantA->id, 'user_id' => $userA->id,
            'company_id' => \App\Models\Company::create([
                'tenant_id' => $tenantA->id, 'name' => 'PT A',
            ])->id,
            'employee_no' => 'EMP-A-001', 'full_name' => 'User A', 'gender' => 'M',
            'employment_status' => 'active', 'join_date' => now()->toDateString(),
        ]);

        $typeB = LeaveType::create([
            'tenant_id' => $tenantB->id, 'code' => 'TAHUNAN',
            'name' => 'Cuti Tahunan', 'is_active' => true,
        ]);

        Sanctum::actingAs($userA, ['leave:write']);

        // Coba ajukan cuti memakai leave_type milik tenant B → harus 404
        $this->postJson('/api/v1/leave/requests', [
            'leave_type_id' => $typeB->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'test',
        ])->assertNotFound();

        // Daftar tipe cuti tenant A tidak boleh memuat milik tenant B
        $this->getJson('/api/v1/leave/types')
            ->assertOk()
            ->assertJsonMissing(['id' => $typeB->id]);
    }

    public function test_user_without_tenant_gets_403_on_api(): void
    {
        $user = User::create([
            'tenant_id' => null, 'name' => 'No Tenant',
            'email' => 'notenant@test.id', 'password' => bcrypt('password'), 'is_active' => true,
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/leave/types')->assertForbidden();
    }
}
