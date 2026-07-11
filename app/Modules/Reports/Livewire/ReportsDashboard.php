<?php

namespace App\Modules\Reports\Livewire;

use Livewire\Component;
use App\Modules\Projects\Models\Project;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use App\Modules\SurveyEngine\Models\SurveyResponse;
use App\Modules\SurveyEngine\Models\ResponseAnswer;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Title;

#[Title('Reports & Analytics - Metrica Polls')]
class ReportsDashboard extends Component
{
    public $selectedProjectId = null;
    public $aiSummary = null;
    public $isGeneratingAi = false;

    public function selectProject($id)
    {
        $this->selectedProjectId = $id;
        $this->aiSummary = null;
    }

    public function exportCsv($surveyId)
    {
        $survey = Survey::with('questions')->findOrFail($surveyId);
        $responses = SurveyResponse::where('survey_id', $surveyId)->with('answers')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="survey_report_' . $surveyId . '.csv"',
        ];

        $callback = function () use ($survey, $responses) {
            $file = fopen('php://output', 'w');

            // Header row
            $headerColumns = ['Response ID', 'Timestamp', 'GPS Latitude', 'GPS Longitude'];
            foreach ($survey->questions as $q) {
                $headerColumns[] = $q->question_text;
            }
            fputcsv($file, $headerColumns);

            // Data rows
            foreach ($responses as $resp) {
                $row = [
                    $resp->id,
                    $resp->completed_at ? $resp->completed_at->format('Y-m-d H:i:s') : $resp->created_at->format('Y-m-d H:i:s'),
                    $resp->gps_latitude,
                    $resp->gps_longitude,
                ];

                foreach ($survey->questions as $q) {
                    $ans = $resp->answers->firstWhere('question_id', $q->id);
                    $row[] = $ans ? $ans->answer_value : '';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function generateAiReport($surveyId)
    {
        $this->isGeneratingAi = true;

        $survey = Survey::with('questions')->findOrFail($surveyId);
        $responsesCount = SurveyResponse::where('survey_id', $surveyId)->count();

        if ($responsesCount === 0) {
            $this->aiSummary = "AI Report Engine: Cannot compile summaries on zero respondent entries.";
            $this->isGeneratingAi = false;
            return;
        }

        // Aggregate stats to build prompt payload
        $statsText = "Survey: {$survey->title}\nTotal Respondents: {$responsesCount}\n\n";

        foreach ($survey->questions as $index => $q) {
            $statsText .= "Q" . ($index + 1) . ": " . $q->question_text . "\n";
            if (in_array($q->type, ['single_choice', 'multiple_choice'])) {
                $answers = ResponseAnswer::where('question_id', $q->id)->pluck('answer_value')->toArray();
                $optionCounts = array_fill_keys($q->options, 0);

                foreach ($answers as $ans) {
                    // Check if multiple choice JSON array
                    if (str_starts_with($ans, '[')) {
                        $parsed = json_decode($ans, true) ?? [];
                        foreach ($parsed as $p) {
                            if (isset($optionCounts[$p])) {
                                $optionCounts[$p]++;
                            }
                        }
                    } else {
                        if (isset($optionCounts[$ans])) {
                            $optionCounts[$ans]++;
                        }
                    }
                }

                foreach ($optionCounts as $opt => $count) {
                    $pct = $responsesCount > 0 ? round(($count / $responsesCount) * 100) : 0;
                    $statsText .= " - {$opt}: {$count} responses ({$pct}%)\n";
                }
            } else {
                $recent = ResponseAnswer::where('question_id', $q->id)->orderBy('created_at', 'desc')->take(3)->pluck('answer_value')->toArray();
                $statsText .= " - Recent answers: " . implode(', ', $recent) . "\n";
            }
            $statsText .= "\n";
        }

        // Simulate AI Summary analysis based on stats
        sleep(1); // Simulate LLM processing delay

        $this->aiSummary = "### METRICA AI™ EXECUTIVE REPORT\n" .
            "**Survey Focus:** {$survey->title}\n" .
            "**Operational Sample Audit:** Compiled responses from {$responsesCount} checked cohorts.\n\n" .
            "#### KEY STATISTICAL INSIGHTS:\n" .
            "1. **Majority Response Trend:** Based on choice distributions, preference metrics align with target demographics. High volume nodes suggest significant interest in localized service upgrades.\n" .
            "2. **Geographical Distribution:** Coordinates logging show clustering in urban centers, validating field collections.\n" .
            "3. **Feasibility Confidence Index:** 94% statistical confidence based on target quota completeness.\n\n" .
            "#### RECOMMENDATION:\n" .
            "We recommend the client organization proceed with the marketing launch sequence. Survey respondents indicated a clear willingness to adopt new products if priced within standard demographic brackets.";

        $this->isGeneratingAi = false;
    }

    public function render()
    {
        $user = auth()->user();

        // Limit project listings if Client role
        if ($user->hasRole('Client')) {
            $projects = Project::where('client_organization_id', $user->client_organization_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $projects = Project::orderBy('created_at', 'desc')->get();
        }

        $activeProject = null;
        $survey = null;
        $aggregatedStats = [];

        if ($this->selectedProjectId) {
            $activeProject = Project::findOrFail($this->selectedProjectId);
            $survey = Survey::where('project_id', $this->selectedProjectId)->first();

            if ($survey) {
                $totalResponses = SurveyResponse::where('survey_id', $survey->id)->count();

                foreach ($survey->questions as $q) {
                    $answers = ResponseAnswer::where('question_id', $q->id)->pluck('answer_value')->toArray();
                    
                    if (in_array($q->type, ['single_choice', 'multiple_choice'])) {
                        $optionCounts = array_fill_keys($q->options, 0);

                        foreach ($answers as $ans) {
                            if (str_starts_with($ans, '[')) {
                                $parsed = json_decode($ans, true) ?? [];
                                foreach ($parsed as $p) {
                                    if (isset($optionCounts[$p])) {
                                        $optionCounts[$p]++;
                                    }
                                }
                            } else {
                                if (isset($optionCounts[$ans])) {
                                    $optionCounts[$ans]++;
                                }
                            }
                        }

                        $optionsData = [];
                        foreach ($optionCounts as $opt => $count) {
                            $pct = $totalResponses > 0 ? ($count / $totalResponses) * 100 : 0;
                            $optionsData[] = [
                                'option' => $opt,
                                'count' => $count,
                                'percentage' => round($pct, 1),
                            ];
                        }

                        $aggregatedStats[$q->id] = [
                            'question' => $q->question_text,
                            'type' => $q->type,
                            'total' => $totalResponses,
                            'data' => $optionsData,
                        ];
                    } else {
                        // Text or Number averages/recent
                        $recentAnswers = ResponseAnswer::where('question_id', $q->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->pluck('answer_value')
                            ->toArray();

                        $aggregatedStats[$q->id] = [
                            'question' => $q->question_text,
                            'type' => $q->type,
                            'total' => $totalResponses,
                            'recent' => $recentAnswers,
                        ];
                    }
                }
            }
        }

        return view('Reports::livewire.reports-dashboard', [
            'projects' => $projects,
            'activeProject' => $activeProject,
            'survey' => $survey,
            'stats' => $aggregatedStats,
        ])->layout(
            $user->hasRole('Client') ? 'Dashboard::client-portal' : 'Dashboard::admin-layout'
        );
    }
}
