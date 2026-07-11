<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        
        // Seed settings
        $this->artisan('db:seed', ['--class' => 'SettingsSeeder']);
    }

    /**
     * Test Settings Database persistency.
     */
    public function test_settings_get_and_set_values(): void
    {
        Setting::setValue('site_title', 'Metrica Analytics');
        $this->assertEquals('Metrica Analytics', Setting::getValue('site_title'));

        $this->assertDatabaseHas('settings', [
            'key' => 'site_title',
            'value' => 'Metrica Analytics',
        ]);
    }

    /**
     * Test route security.
     */
    public function test_only_authorized_staff_can_view_settings(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Admin can view
        $this->actingAs($admin)->get(route('admin.settings'))->assertStatus(200);

        // Panelist blocked
        $this->actingAs($panelist)->get(route('admin.settings'))->assertStatus(403);
    }

    /**
     * Test Maintenance Mode Middleware.
     */
    public function test_maintenance_mode_intercepts_guests_and_allows_staff(): void
    {
        // 1. Initially disabled, public index should load
        $this->get(route('corporate.index'))->assertStatus(200);

        // 2. Enable maintenance mode
        Setting::setValue('maintenance_mode', '1');

        // 3. Guest should be redirected to maintenance page
        $this->get(route('corporate.index'))->assertRedirect(route('public.maintenance'));
        $this->get(route('public.maintenance'))->assertStatus(200);

        // 4. Staff bypass
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $this->actingAs($admin)->get(route('corporate.index'))->assertStatus(200);
    }

    /**
     * Test saving SMTP settings independently.
     */
    public function test_admin_can_save_smtp_independently(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $this->actingAs($admin);

        \Livewire\Livewire::test(\App\Modules\Dashboard\Livewire\SettingsManagement::class)
            ->set('mail_host', 'mail.smtp.com')
            ->set('mail_port', 587)
            ->set('mail_encryption', 'tls')
            ->set('mail_from_address', 'test@metricapolls.com')
            ->set('mail_from_name', 'Metrica Test')
            ->call('saveSmtp')
            ->assertHasNoErrors()
            ->assertSee('SMTP credentials saved successfully.');

        $this->assertEquals('mail.smtp.com', Setting::getValue('mail_host'));
        $this->assertEquals('587', Setting::getValue('mail_port'));
    }

    /**
     * Test sending dynamic SMTP test emails.
     */
    public function test_admin_can_send_test_mail(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $this->actingAs($admin);

        \Illuminate\Support\Facades\Mail::fake();

        \Livewire\Livewire::test(\App\Modules\Dashboard\Livewire\SettingsManagement::class)
            ->set('mail_host', 'mail.smtp.com')
            ->set('mail_port', 587)
            ->set('mail_encryption', 'tls')
            ->set('mail_from_address', 'test@metricapolls.com')
            ->set('mail_from_name', 'Metrica Test')
            ->set('testEmail', 'recipient@gmail.com')
            ->call('sendTestMail')
            ->assertHasNoErrors()
            ->assertSee('Test email sent successfully to');

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\CustomConfigurableMail::class);
    }
}
