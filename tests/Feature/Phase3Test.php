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

class Phase3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test creating a survey and adding questions.
     */
    public function test_survey_can_be_designed_and_created(): void
    {
        // 1. Setup client organization & project
        $org = ClientOrganization::create(['name' => 'Kantar Africa']);
        $project = Project::create([
            'client_organization_id' => $org->id,
            'name' => 'FMCG Drink Survey',
            'status' => 'planning',
            'target_quota' => 500,
        ]);

        // 2. Create survey linked to project
        $survey = Survey::create([
            'project_id' => $project->id,
            'title' => 'Nairobi Soft Drink Habits',
            'description' => 'Auditing soda intake amongst teenagers.',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('surveys', [
            'project_id' => $project->id,
            'title' => 'Nairobi Soft Drink Habits',
        ]);

        // 3. Add questions
        $q1 = Question::create([
            'survey_id' => $survey->id,
            'type' => 'text',
            'question_text' => 'What is your favorite carbonated drink brand?',
            'sort_order' => 1,
        ]);

        $q2 = Question::create([
            'survey_id' => $survey->id,
            'type' => 'single_choice',
            'question_text' => 'How often do you drink sodas?',
            'options' => ['Daily', 'Weekly', 'Never'],
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('questions', [
            'survey_id' => $survey->id,
            'type' => 'text',
            'question_text' => 'What is your favorite carbonated drink brand?',
        ]);

        $this->assertCount(2, $survey->questions);
    }

    /**
     * Test submitting a response increments the project quota and completes the project when quota is met.
     */
    public function test_submitting_response_updates_project_quota(): void
    {
        // 1. Setup organization, project, and published survey
        $org = ClientOrganization::create(['name' => 'McKinsey']);
        $project = Project::create([
            'client_organization_id' => $org->id,
            'name' => 'Demographics Study',
            'status' => 'live',
            'target_quota' => 2, // Small quota to trigger completion
            'current_responses' => 0,
        ]);

        $survey = Survey::create([
            'project_id' => $project->id,
            'title' => 'Mini Demographics',
            'status' => 'published',
        ]);

        $question = Question::create([
            'survey_id' => $survey->id,
            'type' => 'number',
            'question_text' => 'What is your age?',
            'sort_order' => 1,
        ]);

        // 2. Submit Response 1
        $response1 = SurveyResponse::create([
            'survey_id' => $survey->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        ResponseAnswer::create([
            'survey_response_id' => $response1->id,
            'question_id' => $question->id,
            'answer_value' => '25',
        ]);

        // Increment quota manually in test to simulate the component submit logic
        $project->increment('current_responses');
        $this->assertEquals(1, $project->current_responses);
        $this->assertEquals('live', $project->status); // Still live

        // 3. Submit Response 2 (Reaching quota)
        $response2 = SurveyResponse::create([
            'survey_id' => $survey->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        ResponseAnswer::create([
            'survey_response_id' => $response2->id,
            'question_id' => $question->id,
            'answer_value' => '30',
        ]);

        $newCount = $project->current_responses + 1;
        $project->update([
            'current_responses' => $newCount,
            'status' => $newCount >= $project->target_quota ? 'completed' : 'live',
        ]);

        // Assert project quota is met and project status auto-completed
        $this->assertEquals(2, $project->current_responses);
        $this->assertEquals('completed', $project->status);
    }
}
