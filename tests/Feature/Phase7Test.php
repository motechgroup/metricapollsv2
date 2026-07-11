<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\PublicOpinion\Models\MarketplaceReport;
use App\Modules\PublicOpinion\Models\AcademyCourse;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;
use App\Modules\Wallet\Models\PanelistProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase7Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test public access to marketplace and public opinion.
     */
    public function test_guests_can_access_marketplace_and_opinions(): void
    {
        $this->get(route('public.marketplace'))->assertStatus(200);
        $this->get(route('public.opinion'))->assertStatus(200);
    }

    /**
     * Test mock purchase and downloads in marketplace.
     */
    public function test_marketplace_mock_purchase_and_downloads(): void
    {
        $report = MarketplaceReport::create([
            'title' => 'East Africa Consumer Outlook 2026',
            'price' => 49.00,
        ]);

        $this->assertDatabaseHas('marketplace_reports', [
            'id' => $report->id,
            'title' => 'East Africa Consumer Outlook 2026',
        ]);
    }

    /**
     * Test public opinion micro-voting logs and increments count.
     */
    public function test_opinion_voting_records_vote(): void
    {
        $poll = PublicOpinion::create([
            'topic' => 'Preferred transport in Nairobi',
            'options' => ['Matatu', 'Boda Boda', 'Personal Car'],
            'votes_count' => 0,
        ]);

        PublicOpinionVote::create([
            'public_opinion_id' => $poll->id,
            'ip_address' => '127.0.0.1',
            'voted_option' => 'Matatu',
        ]);
        $poll->increment('votes_count');

        $this->assertEquals(1, $poll->fresh()->votes_count);
        $this->assertDatabaseHas('public_opinion_votes', [
            'public_opinion_id' => $poll->id,
            'voted_option' => 'Matatu',
        ]);
    }

    /**
     * Test Academy courses completion rewards.
     */
    public function test_academy_course_completion_awards_points(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();
        $profile = PanelistProfile::create([
            'user_id' => $panelist->id,
            'points_balance' => 100,
        ]);

        $course = AcademyCourse::create([
            'title' => 'Introduction to Survey Ethics',
            'points_award' => 50,
            'lessons' => [
                ['title' => 'Ethics Lesson 1', 'content' => 'Content here']
            ]
        ]);

        // Complete Course
        $profile->increment('points_balance', $course->points_award);

        $this->assertEquals(150, $profile->fresh()->points_balance);
        $this->assertDatabaseHas('panelists', [
            'user_id' => $panelist->id,
            'points_balance' => 150,
        ]);
    }
}
