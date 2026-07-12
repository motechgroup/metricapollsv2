<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_pages_render_successfully()
    {
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $this->seed(\Database\Seeders\SettingsSeeder::class);

        // Get terms and privacy pages
        $response1 = $this->get(route('corporate.terms'));
        $response1->assertStatus(200);
        $response1->assertSee('Terms of Service');
        $response1->assertSee('Anti-Fraud');

        $response2 = $this->get(route('corporate.privacy'));
        $response2->assertStatus(200);
        $response2->assertSee('Privacy Policy');
        $response2->assertSee('GDPR Compliance');
    }

    public function test_sitemap_renders_successfully()
    {
        $response = $this->get(route('corporate.sitemap'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertSee('urlset');
    }
}
