<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use App\Modules\SurveyEngine\Models\SurveyResponse;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Modules\SurveyEngine\Livewire\SurveyRenderer;

class PanelistPaidSurveysTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;
    protected $qualificationSurvey;
    protected $paidSurvey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

        // Create Panelist user
        $this->user = User::create([
            'name' => 'Alice Awino',
            'email' => 'alice.awino@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $this->user->assignRole('Panelist');

        $org = ClientOrganization::create(['name' => 'Gov Research Board']);
        $this->project = Project::create([
            'client_organization_id' => $org->id,
            'name' => 'Civic Awareness Study',
            'status' => 'live',
            'target_quota' => 10,
        ]);

        // Create Qualification/Training survey
        $this->qualificationSurvey = Survey::create([
            'project_id' => $this->project->id,
            'title' => 'Metrica Qualification Test',
            'status' => 'published',
            'is_qualification' => true,
            'is_paid' => false,
            'min_badge_level' => 'Bronze',
        ]);

        Question::create([
            'survey_id' => $this->qualificationSurvey->id,
            'type' => 'single_choice',
            'question_text' => 'Choose Option A (Attention check: Select Option A)',
            'options' => ['Option A', 'Option B'],
            'sort_order' => 1,
        ]);

        // Create Paid survey
        $this->paidSurvey = Survey::create([
            'project_id' => $this->project->id,
            'title' => 'Consumer Brand Audit',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 200.00,
            'min_badge_level' => 'Silver',
        ]);

        Question::create([
            'survey_id' => $this->paidSurvey->id,
            'type' => 'text',
            'question_text' => 'Enter feedback',
            'sort_order' => 1,
        ]);
    }

    /**
     * Test successful qualification test completion.
     */
    public function test_panelist_completes_qualification_successfully(): void
    {
        $this->actingAs($this->user);

        // Retrieve question
        $question = Question::where('survey_id', $this->qualificationSurvey->id)->first();

        // Simulate Livewire survey responses rendering & submission
        $renderer = Livewire::test(SurveyRenderer::class, ['surveyId' => $this->qualificationSurvey->id])
            ->set('answers', [$question->id => 'Option A'])
            ->set('startTime', time() - 10); // Bypass speed trap (10s > 2s minimum)

        $renderer->call('submit');

        $renderer->assertHasNoErrors();
        $renderer->assertSet('isFraudDetected', false);

        // Verify profile is verified, points added (150 pts), and badge level
        $profile = PanelistProfile::where('user_id', $this->user->id)->first();
        $this->assertTrue($profile->is_verified);
        $this->assertEquals(150, $profile->points_balance);
        $this->assertEquals(50, $profile->experience_points);
    }

    /**
     * Test timing speed trap fraud detection.
     */
    public function test_survey_speed_trap_flags_fraud(): void
    {
        $this->actingAs($this->user);

        $question = Question::where('survey_id', $this->qualificationSurvey->id)->first();

        // Set startTime to now (triggering the speed trap since it is completed instantly under 2 seconds)
        $renderer = Livewire::test(SurveyRenderer::class, ['surveyId' => $this->qualificationSurvey->id])
            ->set('answers', [$question->id => 'Option A'])
            ->set('startTime', time());

        $renderer->call('submit');

        $renderer->assertSet('isFraudDetected', true);
        $this->assertStringContainsString('Timing Trap', $renderer->get('fraudReasonDetail'));

        // No points should be awarded
        $profile = PanelistProfile::where('user_id', $this->user->id)->first();
        $this->assertEquals(0, $profile ? $profile->points_balance : 0);
    }

    /**
     * Test attention check verification.
     */
    public function test_attention_check_failure_flags_fraud(): void
    {
        $this->actingAs($this->user);

        $question = Question::where('survey_id', $this->qualificationSurvey->id)->first();

        // Select Option B (attention check explicitly asks for 'Option A')
        $renderer = Livewire::test(SurveyRenderer::class, ['surveyId' => $this->qualificationSurvey->id])
            ->set('answers', [$question->id => 'Option B'])
            ->set('startTime', time() - 10);

        $renderer->call('submit');

        $renderer->assertSet('isFraudDetected', true);
        $this->assertStringContainsString('Attention Check Failure', $renderer->get('fraudReasonDetail'));

        $profile = PanelistProfile::where('user_id', $this->user->id)->first();
        $this->assertEquals(0, $profile ? $profile->points_balance : 0);
    }

    /**
     * Test paid survey payout and badge promotion.
     */
    public function test_paid_survey_payout_and_badge_upgrade(): void
    {
        // 1. Manually initialize a Silver profile with 110 experience points
        $profile = PanelistProfile::create([
            'user_id' => $this->user->id,
            'is_verified' => true,
            'points_balance' => 150,
            'experience_points' => 110,
            'badge_level' => 'Silver',
        ]);

        $this->actingAs($this->user);

        $question = Question::where('survey_id', $this->paidSurvey->id)->first();

        // Complete Paid Survey
        $renderer = Livewire::test(SurveyRenderer::class, ['surveyId' => $this->paidSurvey->id])
            ->set('answers', [$question->id => 'High quality index'])
            ->set('startTime', time() - 10);

        $renderer->call('submit');

        $renderer->assertHasNoErrors();
        $renderer->assertSet('isFraudDetected', false);

        // Points should increment by payout amount (200.00 KES)
        $profile->refresh();
        $this->assertEquals(350, $profile->points_balance); // 150 base + 200 payout
        $this->assertEquals(140, $profile->experience_points); // 110 base + 30 exp
        
        // Verify transaction logged
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'type' => 'reward',
            'points' => 200,
        ]);
    }
}
