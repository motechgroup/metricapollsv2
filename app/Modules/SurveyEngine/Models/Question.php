<?php

namespace App\Modules\SurveyEngine\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'survey_id',
        'type',
        'question_text',
        'options',
        'rules',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'rules' => 'array',
    ];

    /**
     * Get the survey containing this question.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the answers given for this question.
     */
    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class);
    }
}
