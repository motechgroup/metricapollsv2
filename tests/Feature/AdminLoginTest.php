<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Livewire\Livewire;
use App\Modules\Authentication\Livewire\Login;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_route_renders_successfully()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Metrica Staff Login');
    }

    public function test_admin_can_login_directly_without_otp()
    {
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'staff_admin@metricapolls.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
            ]
        );
        $admin->assignRole('Admin');

        Livewire::test(Login::class)
            ->set('email', 'staff_admin@metricapolls.com')
            ->set('password', 'password123')
            ->set('isAdminLogin', true)
            ->call('login')
            ->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_panelist_cannot_login_on_admin_login_route()
    {
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $panelist = User::create([
            'name' => 'Panelist User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password123'),
        ]);
        $panelist->assignRole('Panelist');

        Livewire::test(Login::class)
            ->set('email', 'user@gmail.com')
            ->set('password', 'password123')
            ->set('isAdminLogin', true)
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }
}
