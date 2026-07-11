<?php

namespace App\Modules\SurveyEngine\Livewire;

use Livewire\Component;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\Projects\Models\Project;
use Livewire\Attributes\Title;

#[Title('Manage Surveys & Tests - Metrica Polls')]
class SurveyManager extends Component
{
    public $search = '';
    public $filterType = 'all'; // all, paid, qualification

    // Form fields
    public $surveyId = null;
    public $title = '';
    public $description = '';
    public $project_id = '';
    public $status = 'draft';
    public $is_paid = false;
    public $payout_amount = 0.00;
    public $is_qualification = false;
    public $min_badge_level = 'Bronze';

    public $isFormOpen = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'project_id' => 'required|exists:projects,id',
        'status' => 'required|string|in:draft,published,archived',
        'is_paid' => 'required|boolean',
        'payout_amount' => 'required|numeric|min:0',
        'is_qualification' => 'required|boolean',
        'min_badge_level' => 'required|string|in:Bronze,Silver,Gold',
    ];

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->isFormOpen = true;

        if ($id) {
            $survey = Survey::findOrFail($id);
            $this->surveyId = $survey->id;
            $this->title = $survey->title;
            $this->description = $survey->description;
            $this->project_id = $survey->project_id;
            $this->status = $survey->status;
            $this->is_paid = $survey->is_paid;
            $this->payout_amount = $survey->payout_amount;
            $this->is_qualification = $survey->is_qualification;
            $this->min_badge_level = $survey->min_badge_level;
        } else {
            $this->surveyId = null;
            $this->title = '';
            $this->description = '';
            // Auto default to first project if available
            $firstProj = Project::first();
            $this->project_id = $firstProj ? $firstProj->id : '';
            $this->status = 'draft';
            $this->is_paid = false;
            $this->payout_amount = 0.00;
            $this->is_qualification = false;
            $this->min_badge_level = 'Bronze';
        }
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->surveyId) {
            $survey = Survey::findOrFail($this->surveyId);
            $survey->update([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'status' => $this->status,
                'is_paid' => $this->is_paid,
                'payout_amount' => $this->payout_amount,
                'is_qualification' => $this->is_qualification,
                'min_badge_level' => $this->min_badge_level,
            ]);
            session()->flash('success', "Survey '{$this->title}' updated successfully.");
        } else {
            Survey::create([
                'title' => $this->title,
                'description' => $this->description,
                'project_id' => $this->project_id,
                'status' => $this->status,
                'is_paid' => $this->is_paid,
                'payout_amount' => $this->payout_amount,
                'is_qualification' => $this->is_qualification,
                'min_badge_level' => $this->min_badge_level,
            ]);
            session()->flash('success', "New survey '{$this->title}' registered.");
        }

        $this->closeForm();
    }

    public function delete($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();
        session()->flash('success', 'Survey campaign removed successfully.');
    }

    public function render()
    {
        $query = Survey::with(['project']);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->filterType === 'paid') {
            $query->where('is_paid', true);
        } elseif ($this->filterType === 'qualification') {
            $query->where('is_qualification', true);
        }

        $surveys = $query->orderBy('created_at', 'desc')->get();
        $projects = Project::all();

        return view('SurveyEngine::livewire.survey-manager', [
            'surveys' => $surveys,
            'projects' => $projects,
        ])->layout('Dashboard::admin-layout');
    }
}
