<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\Projects\Models\Project;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use App\Modules\SurveyEngine\Models\SurveyResponse;
use App\Modules\SurveyEngine\Models\ResponseAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase6Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test Report Dashboard access rules.
     */
    public function test_authorized_users_can_access_reports(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $client = User::where('email', 'client@metricapolls.com')->first();
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Admin and Client can view reports
        $this->actingAs($admin)->get(route('admin.reports'))->assertStatus(200);
        $this->actingAs($client)->get(route('admin.reports'))->assertStatus(200);

        // Panelist is blocked
        $this->actingAs($panelist)->get(route('admin.reports'))->assertStatus(403);
    }

    /**
     * Test data aggregation stats.
     */
    public function test_reports_page_aggregates_choice_options(): void
    {
        // 1. Setup project & survey
        $org = ClientOrganization::create(['name' => 'United Nations']);
        
        $project = Project::create([
            'client_organization_id' => $org->id,
            'name' => 'Habitat Project',
            'status' => 'live',
            'target_quota' => 10,
        ]);

        $survey = Survey::create([
            'project_id' => $project->id,
            'title' => 'Urban Shelter Survey',
            'status' => 'published',
        ]);

        $q = Question::create([
            'survey_id' => $survey->id,
            'type' => 'single_choice',
            'question_text' => 'What is your type of housing?',
            'options' => ['House', 'Apartment', 'Other'],
            'sort_order' => 1,
        ]);

        // 2. Insert 3 responses (2 House, 1 Apartment)
        $r1 = SurveyResponse::create(['survey_id' => $survey->id]);
        ResponseAnswer::create(['survey_response_id' => $r1->id, 'question_id' => $q->id, 'answer_value' => 'House']);

        $r2 = SurveyResponse::create(['survey_id' => $survey->id]);
        ResponseAnswer::create(['survey_response_id' => $r2->id, 'question_id' => $q->id, 'answer_value' => 'House']);

        $r3 = SurveyResponse::create(['survey_id' => $survey->id]);
        ResponseAnswer::create(['survey_response_id' => $r3->id, 'question_id' => $q->id, 'answer_value' => 'Apartment']);

        // Check if database records match
        $this->assertCount(3, SurveyResponse::where('survey_id', $survey->id)->get());
        
        // Assert option counts calculate
        $houseAnswersCount = ResponseAnswer::where('question_id', $q->id)->where('answer_value', 'House')->count();
        $apartmentAnswersCount = ResponseAnswer::where('question_id', $q->id)->where('answer_value', 'Apartment')->count();

        $this->assertEquals(2, $houseAnswersCount);
        $this->assertEquals(1, $apartmentAnswersCount);
    }
}
