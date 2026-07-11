<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\Projects\Models\Project;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use App\Modules\FieldOperations\Models\FieldAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase5Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test sync API endpoint stores response batches and increments quotas.
     */
    public function test_sync_api_records_responses_and_updates_quotas(): void
    {
        // 1. Setup entities
        $org = ClientOrganization::create(['name' => 'World Bank']);
        
        $project = Project::create([
            'client_organization_id' => $org->id,
            'name' => 'Poverty Index Survey',
            'status' => 'live',
            'target_quota' => 10,
            'current_responses' => 0,
        ]);

        $survey = Survey::create([
            'project_id' => $project->id,
            'title' => 'Household Income Audit',
            'status' => 'published',
        ]);

        $q1 = Question::create([
            'survey_id' => $survey->id,
            'type' => 'number',
            'question_text' => 'Number of household members?',
            'sort_order' => 1,
        ]);

        $agent = User::create([
            'name' => 'Agent 007',
            'email' => 'agent@metricapolls.com',
            'password' => bcrypt('Password123'),
            'status' => 'active',
        ]);
        $agent->assignRole('Field Agent');

        $assignment = FieldAssignment::create([
            'project_id' => $project->id,
            'field_agent_id' => $agent->id,
            'target_submissions' => 5,
            'completed_submissions' => 0,
            'status' => 'assigned',
        ]);

        // 2. Mock payload
        $payload = [
            'field_agent_id' => $agent->id,
            'responses' => [
                [
                    'survey_id' => $survey->id,
                    'gps_latitude' => -1.2980,
                    'gps_longitude' => 36.8120,
                    'answers' => [
                        $q1->id => '4',
                    ]
                ],
                [
                    'survey_id' => $survey->id,
                    'gps_latitude' => -1.2995,
                    'gps_longitude' => 36.8140,
                    'answers' => [
                        $q1->id => '6',
                    ]
                ]
            ]
        ];

        // 3. Post to API
        $response = $this->postJson(route('api.field.sync'), $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);

        // 4. Assert Database updates
        $this->assertDatabaseHas('survey_responses', [
            'survey_id' => $survey->id,
            'field_agent_id' => $agent->id,
        ]);

        $this->assertDatabaseHas('response_answers', [
            'question_id' => $q1->id,
            'answer_value' => '4',
        ]);

        $this->assertDatabaseHas('response_answers', [
            'question_id' => $q1->id,
            'answer_value' => '6',
        ]);

        // 5. Assert Assignment Completed Submissions count incremented
        $this->assertEquals(2, $assignment->fresh()->completed_submissions);

        // 6. Assert Overall Project responses incremented
        $this->assertEquals(2, $project->fresh()->current_responses);
    }
}
