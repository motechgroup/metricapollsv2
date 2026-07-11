<?php

namespace App\Modules\SurveyEngine\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseAnswer extends Model
{
    protected $fillable = [
        'survey_response_id',
        'question_id',
        'answer_value',
    ];

    /**
     * Get the survey response session owning this answer.
     */
    public function surveyResponse()
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    /**
     * Get the question this answer responds to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
