<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions for tests
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test public corporate pages return successful status code.
     */
    public function test_corporate_pages_are_accessible(): void
    {
        $this->get(route('corporate.index'))->assertStatus(200);
        $this->get(route('corporate.features'))->assertStatus(200);
        $this->get(route('corporate.pricing'))->assertStatus(200);
        $this->get(route('corporate.about'))->assertStatus(200);
        $this->get(route('corporate.contact'))->assertStatus(200);
    }

    /**
     * Test login and registration pages are accessible.
     */
    public function test_auth_pages_are_accessible(): void
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('auth.register'))->assertStatus(200);
    }

    /**
     * Test guests are redirected from admin area.
     */
    public function test_guests_cannot_access_admin_panel(): void
    {
        $response = $this->get(route('admin.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        
        $response = $this->actingAs($admin)
            ->get(route('admin.index'));

        $response->assertStatus(200);
    }

    /**
     * Test panelist is blocked from admin dashboard.
     */
    public function test_panelist_cannot_access_admin_panel(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        $response = $this->actingAs($panelist)
            ->get(route('admin.index'));

        // Should return 403 Forbidden since panelist role lacks the admin role
        $response->assertStatus(403);
    }
}
