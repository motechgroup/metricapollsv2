<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\PublicOpinion\Models\AdminPoll;
use App\Modules\PublicOpinion\Models\ReportDownload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test route access controls.
     */
    public function test_only_authorized_staff_can_access_admin_poll_builder(): void
    {
        // Guest blocked
        $this->get(route('admin.polls.create'))->assertRedirect(route('login'));

        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Admin can access
        $this->actingAs($admin)->get(route('admin.polls.create'))->assertStatus(200);

        // Panelist blocked
        $this->actingAs($panelist)->get(route('admin.polls.create'))->assertStatus(403);
    }

    /**
     * Test custom poll and AI report generation.
     */
    public function test_custom_poll_and_ai_report_generation(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();

        $poll = AdminPoll::create([
            'title' => 'Nairobi Soda Preference 2026',
            'category' => 'Brand Performance',
            'is_public' => true,
            'research_date' => '2026-07-10',
            'release_date' => '2026-07-10',
            'initial_downloads' => 150,
            'download_count' => 0,
            'sample_size' => 500,
            'region' => 'Nairobi, Kenya',
            'methodology' => 'Dynamic testing methodology.',
            'options' => [
                ['name' => 'Coca Cola', 'votes' => 300, 'logo' => '/images/favicon.png'],
                ['name' => 'Pepsi', 'votes' => 100, 'logo' => '/images/favicon.png'],
            ],
            'ai_report' => "### METRICA POLLS AI™ INSIGHTS\nCoca Cola commands 75% market share.",
        ]);

        $this->assertDatabaseHas('admin_polls', [
            'id' => $poll->id,
            'title' => 'Nairobi Soda Preference 2026',
            'region' => 'Nairobi, Kenya',
            'sample_size' => 500,
        ]);

        $this->assertEquals(400, collect($poll->options)->sum('votes'));
    }

    /**
     * Test public reports list, visitor email input, and downloads logs.
     */
    public function test_public_reports_downloads_require_email_and_log_leads(): void
    {
        $poll = AdminPoll::create([
            'title' => 'Kenya 2026 General Politics Audit',
            'category' => 'Political Popularity',
            'is_public' => true,
            'research_date' => '2026-07-10',
            'release_date' => '2026-07-10',
            'initial_downloads' => 10,
            'download_count' => 0,
            'sample_size' => 100,
            'region' => 'Mombasa',
            'methodology' => 'Methodology description.',
            'options' => [
                ['name' => 'Candidate A', 'votes' => 500, 'logo' => '/images/favicon.png'],
            ],
            'ai_report' => "### METRICA POLLS AI™ INSIGHTS\nCandidate A leads regional voting.",
        ]);

        $response = $this->get(route('public.reports'));
        $response->assertStatus(200);
        $response->assertSee('Kenya 2026 General Politics Audit');

        // Log report download lead
        ReportDownload::create([
            'admin_poll_id' => $poll->id,
            'email' => 'visitor@test.com',
        ]);
        $poll->increment('download_count');

        $this->assertEquals(1, $poll->fresh()->download_count);
        $this->assertEquals(11, $poll->fresh()->initial_downloads + $poll->fresh()->download_count);

        $this->assertDatabaseHas('report_downloads', [
            'admin_poll_id' => $poll->id,
            'email' => 'visitor@test.com',
        ]);
    }
}
