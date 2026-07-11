<?php

namespace App\Modules\SurveyEngine\Livewire;

use Livewire\Component;
use App\Modules\Projects\Models\Project;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use Livewire\Attributes\Title;

#[Title('Survey Designer - Metrica Polls')]
class SurveyDesigner extends Component
{
    public $projectId;
    public $project;
    public $survey;

    // Survey fields
    public $surveyTitle = '';
    public $surveyDescription = '';
    public $surveyStatus = 'draft';

    // Question form fields
    public $questionText = '';
    public $questionType = 'text'; // text, single_choice, multiple_choice, number
    public $questionOptionsText = ''; // Comma separated list of choices
    public $questionSortOrder = 0;

    protected $rules = [
        'surveyTitle' => 'required|string|max:255',
        'surveyDescription' => 'nullable|string',
        'surveyStatus' => 'required|in:draft,published,archived',
    ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->project = Project::findOrFail($projectId);

        // Fetch or create Survey
        $this->survey = Survey::firstOrCreate(
            ['project_id' => $projectId],
            [
                'title' => $this->project->name . ' Survey',
                'description' => 'A questionnaire for ' . $this->project->name,
                'status' => 'draft',
            ]
        );

        $this->surveyTitle = $this->survey->title;
        $this->surveyDescription = $this->survey->description;
        $this->surveyStatus = $this->survey->status;
    }

    public function saveSurveyMeta()
    {
        $this->validate();

        $this->survey->update([
            'title' => $this->surveyTitle,
            'description' => $this->surveyDescription,
            'status' => $this->surveyStatus,
        ]);

        session()->flash('success', 'Survey details saved successfully.');
    }

    public function addQuestion()
    {
        $this->validate([
            'questionText' => 'required|string|max:500',
            'questionType' => 'required|in:text,single_choice,multiple_choice,number',
            'questionOptionsText' => 'required_if:questionType,single_choice,multiple_choice|string',
        ]);

        $options = null;
        if (in_array($this->questionType, ['single_choice', 'multiple_choice'])) {
            $options = array_map('trim', explode(',', $this->questionOptionsText));
        }

        // Auto increment sort order
        $maxSort = Question::where('survey_id', $this->survey->id)->max('sort_order') ?? 0;

        Question::create([
            'survey_id' => $this->survey->id,
            'type' => $this->questionType,
            'question_text' => $this->questionText,
            'options' => $options,
            'sort_order' => $maxSort + 1,
        ]);

        // Reset form
        $this->questionText = '';
        $this->questionType = 'text';
        $this->questionOptionsText = '';

        session()->flash('success', 'Question added successfully.');
    }

    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        session()->flash('success', 'Question removed from survey.');
    }

    public function render()
    {
        $questions = Question::where('survey_id', $this->survey->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('SurveyEngine::livewire.survey-designer', [
            'questions' => $questions,
        ])->layout('Dashboard::admin-layout');
    }
}
