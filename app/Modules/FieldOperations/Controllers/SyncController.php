<?php

namespace App\Modules\FieldOperations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\SurveyResponse;
use App\Modules\SurveyEngine\Models\ResponseAnswer;
use App\Modules\FieldOperations\Models\FieldAssignment;

class SyncController extends Controller
{
    /**
     * Synchronize batch offline responses collected by field agents.
     */
    public function syncResponses(Request $request)
    {
        $request->validate([
            'field_agent_id' => 'required|exists:users,id',
            'responses' => 'required|array|min:1',
            'responses.*.survey_id' => 'required|exists:surveys,id',
            'responses.*.gps_latitude' => 'nullable|numeric',
            'responses.*.gps_longitude' => 'nullable|numeric',
            'responses.*.answers' => 'required|array',
        ]);

        $syncedIds = [];

        DB::transaction(function () use ($request, &$syncedIds) {
            foreach ($request->responses as $index => $rawResp) {
                $survey = Survey::with('project')->findOrFail($rawResp['survey_id']);
                $project = $survey->project;

                // 1. Log Survey Response
                $response = SurveyResponse::create([
                    'survey_id' => $rawResp['survey_id'],
                    'user_id' => null, // Collected face-to-face, so respondent user is anonymous
                    'field_agent_id' => $request->field_agent_id,
                    'gps_latitude' => $rawResp['gps_latitude'] ?? null,
                    'gps_longitude' => $rawResp['gps_longitude'] ?? null,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // 2. Save Answers
                foreach ($rawResp['answers'] as $qId => $ansVal) {
                    ResponseAnswer::create([
                        'survey_response_id' => $response->id,
                        'question_id' => $qId,
                        'answer_value' => is_array($ansVal) ? json_encode($ansVal) : strval($ansVal),
                    ]);
                }

                // 3. Update Field Assignment Completed Submissions
                $assignment = FieldAssignment::where('project_id', $project->id)
                    ->where('field_agent_id', $request->field_agent_id)
                    ->first();

                if ($assignment) {
                    $newSubmissionsCount = $assignment->completed_submissions + 1;
                    $assignment->update([
                        'completed_submissions' => $newSubmissionsCount,
                        'status' => $newSubmissionsCount >= $assignment->target_submissions ? 'completed' : 'active',
                    ]);
                }

                // 4. Update Project Overall Quota
                $newProjectResponses = $project->current_responses + 1;
                $project->update([
                    'current_responses' => min($project->target_quota, $newProjectResponses),
                    'status' => $newProjectResponses >= $project->target_quota ? 'completed' : $project->status,
                ]);

                $syncedIds[] = $index;
            }
        });

        return response()->json([
            'success' => true,
            'message' => count($syncedIds) . ' offline survey response(s) synchronized successfully.',
            'synced_indexes' => $syncedIds,
        ], 200);
    }
}
