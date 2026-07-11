<?php

namespace App\Modules\Projects\Livewire;

use Livewire\Component;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;
use App\Models\User;
use Livewire\Attributes\Title;

#[Title('Project Management - Metrica Polls')]
class ProjectList extends Component
{
    public $projectId = null;
    public $name = '';
    public $client_organization_id = '';
    public $project_manager_id = '';
    public $budget = null;
    public $status = 'planning';
    public $target_quota = 1000;
    public $current_responses = 0;
    public $start_date = '';
    public $end_date = '';

    public $isFormOpen = false;

    // Simulation helpers
    public $simulatedIncrement = 50;

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->isFormOpen = true;

        if ($id) {
            $project = Project::findOrFail($id);
            $this->projectId = $project->id;
            $this->name = $project->name;
            $this->client_organization_id = $project->client_organization_id;
            $this->project_manager_id = $project->project_manager_id ?? '';
            $this->budget = $project->budget;
            $this->status = $project->status;
            $this->target_quota = $project->target_quota;
            $this->current_responses = $project->current_responses;
            $this->start_date = $project->start_date ?? '';
            $this->end_date = $project->end_date ?? '';
        } else {
            $this->projectId = null;
            $this->name = '';
            $this->client_organization_id = '';
            $this->project_manager_id = '';
            $this->budget = null;
            $this->status = 'planning';
            $this->target_quota = 1000;
            $this->current_responses = 0;
            $this->start_date = '';
            $this->end_date = '';
        }
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'client_organization_id' => 'required|exists:client_organizations,id',
            'project_manager_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,live,paused,completed',
            'target_quota' => 'required|integer|min:1',
            'current_responses' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'client_organization_id' => $this->client_organization_id,
            'name' => $this->name,
            'project_manager_id' => !empty($this->project_manager_id) ? $this->project_manager_id : null,
            'budget' => $this->budget,
            'status' => $this->status,
            'target_quota' => $this->target_quota,
            'current_responses' => $this->current_responses,
            'start_date' => !empty($this->start_date) ? $this->start_date : null,
            'end_date' => !empty($this->end_date) ? $this->end_date : null,
        ];

        if ($this->projectId) {
            $project = Project::findOrFail($this->projectId);
            $project->update($data);
            session()->flash('success', "Project '{$this->name}' updated successfully.");
        } else {
            Project::create($data);
            session()->flash('success', "New project '{$this->name}' created.");
        }

        $this->closeForm();
    }

    /**
     * Simulate gathering responses for the survey (Phase 3 Engine preview).
     */
    public function simulateCollection($id)
    {
        $project = Project::findOrFail($id);
        
        $newResponses = $project->current_responses + intval($this->simulatedIncrement);
        
        $project->update([
            'current_responses' => min($project->target_quota, $newResponses),
            'status' => $newResponses >= $project->target_quota ? 'completed' : 'live',
        ]);

        session()->flash('success', "Simulated collection: Gathered +{$this->simulatedIncrement} submissions for '{$project->name}'.");
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        session()->flash('success', 'Project removed from operations.');
    }

    public function render()
    {
        $projects = Project::with(['clientOrganization', 'projectManager'])
            ->orderBy('created_at', 'desc')
            ->get();

        $organizations = ClientOrganization::all();
        
        // Fetch candidates for Project Managers (Admins and PMs)
        $managers = User::role(['Super Admin', 'Admin', 'Project Manager', 'Field Manager'])->get();

        return view('Projects::livewire.project-list', [
            'projects' => $projects,
            'organizations' => $organizations,
            'managers' => $managers,
        ])->layout('Dashboard::admin-layout');
    }
}
