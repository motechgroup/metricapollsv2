<?php

namespace App\Modules\FieldOperations\Livewire;

use Livewire\Component;
use App\Modules\FieldOperations\Models\FieldAssignment;
use App\Modules\Projects\Models\Project;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title;

#[Title('My Field Assignments - Metrica Polls')]
class AgentAssignments extends Component
{
    public $activeAssignmentId = null;
    public $activeAssignment = null;

    // Simulation fields
    public $localOfflineCache = []; // Simulated IndexDB/local browser cache
    public $offlineAnswers = [];

    public function selectAssignmentForOffline($id)
    {
        $this->activeAssignmentId = $id;
        $this->activeAssignment = FieldAssignment::with('project.survey.questions')->findOrFail($id);
        
        // Initialize answer array
        $this->offlineAnswers = [];
        if ($this->activeAssignment->project->survey) {
            foreach ($this->activeAssignment->project->survey->questions as $q) {
                if ($q->type === 'multiple_choice') {
                    $this->offlineAnswers[$q->id] = [];
                } else {
                    $this->offlineAnswers[$q->id] = '';
                }
            }
        }
    }

    public function saveOfflineResponse()
    {
        // Dynamic offline validation check
        $survey = $this->activeAssignment->project->survey;
        if (!$survey) {
            return;
        }

        foreach ($survey->questions as $q) {
            if (empty($this->offlineAnswers[$q->id])) {
                $this->addError("offlineAnswers.{$q->id}", "This field is required offline.");
                return;
            }
        }

        // Add to offline cache
        $this->localOfflineCache[] = [
            'survey_id' => $survey->id,
            'gps_latitude' => -1.30 + (rand(-100, 100) / 10000), // Random jitter around Nairobi
            'gps_longitude' => 36.82 + (rand(-100, 100) / 10000),
            'answers' => $this->offlineAnswers,
        ];

        // Reset
        $this->activeAssignmentId = null;
        $this->activeAssignment = null;
        $this->offlineAnswers = [];

        session()->flash('success', 'Survey response logged to offline local storage cache.');
    }

    public function syncCache()
    {
        if (empty($this->localOfflineCache)) {
            session()->flash('error', 'No offline responses cached to sync.');
            return;
        }

        // Trigger our REST Sync API endpoint programmatically
        // We will call the SyncController logic directly to keep it fast and self-contained
        $syncController = new \App\Modules\FieldOperations\Controllers\SyncController();
        
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'field_agent_id' => auth()->id(),
            'responses' => $this->localOfflineCache,
        ]);

        $response = $syncController->syncResponses($request);
        $resData = json_decode($response->getContent(), true);

        if ($resData['success'] ?? false) {
            $count = count($this->localOfflineCache);
            $this->localOfflineCache = []; // Clear cache
            session()->flash('success', "Sync complete! Successfully synchronized {$count} offline response(s) to cloud database.");
        } else {
            session()->flash('error', 'Sync failed. Please check network connectivity.');
        }
    }

    public function discardCache()
    {
        $this->localOfflineCache = [];
        session()->flash('success', 'Offline browser response cache cleared.');
    }

    public function cancelOffline()
    {
        $this->activeAssignmentId = null;
        $this->activeAssignment = null;
        $this->offlineAnswers = [];
    }

    public function render()
    {
        $agentId = auth()->id();

        // Auto-seed a Field Assignment if none exist so there's demo data!
        $assignmentCount = FieldAssignment::where('field_agent_id', $agentId)->count();
        if ($assignmentCount === 0) {
            // Find or create a default organization to prevent foreign key issues
            $org = \App\Modules\CRM\Models\ClientOrganization::firstOrCreate(
                ['name' => 'Default Organization']
            );

            // Find or create a project
            $project = Project::firstOrCreate(
                ['name' => 'Nairobi Water Supply Audit'],
                [
                    'client_organization_id' => $org->id,
                    'status' => 'live',
                    'target_quota' => 200,
                    'current_responses' => 0,
                ]
            );

            // Find or create survey
            $survey = Survey::firstOrCreate(
                ['project_id' => $project->id],
                [
                    'title' => 'Nairobi Water Utility Questionnaire',
                    'description' => 'A face-to-face questionnaire audit concerning water utility reliability.',
                    'status' => 'published',
                ]
            );

            // Find or create questions
            if (Question::where('survey_id', $survey->id)->count() === 0) {
                Question::create([
                    'survey_id' => $survey->id,
                    'type' => 'single_choice',
                    'question_text' => 'How many days a week do you get running water?',
                    'options' => ['Daily', '3-5 Days', '1-2 Days', 'Never'],
                    'sort_order' => 1,
                ]);
                Question::create([
                    'survey_id' => $survey->id,
                    'type' => 'text',
                    'question_text' => 'Any specific comments on quality?',
                    'sort_order' => 2,
                ]);
            }

            // Create assignment
            FieldAssignment::create([
                'project_id' => $project->id,
                'field_agent_id' => $agentId,
                'target_submissions' => 50,
                'completed_submissions' => 0,
                'status' => 'assigned',
            ]);
        }

        // Fetch agent assignments
        $assignments = FieldAssignment::where('field_agent_id', $agentId)
            ->with(['project.survey.questions'])
            ->get();

        return view('FieldOperations::livewire.agent-assignments', [
            'assignments' => $assignments,
        ])->layout('Dashboard::admin-layout'); // Using admin-layout for staff portals
    }
}
