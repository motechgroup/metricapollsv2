<?php

namespace App\Modules\SurveyEngine\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id',
        'user_id',
        'field_agent_id',
        'gps_latitude',
        'gps_longitude',
        'status',
        'completed_at',
        'duration_seconds',
        'is_fraudulent',
        'fraud_reason',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'is_fraudulent' => 'boolean',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the survey associated with this response.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the panelist/respondent who completed the survey.
     */
    public function panelist()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the field agent who conducted the interview.
     */
    public function fieldAgent()
    {
        return $this->belongsTo(User::class, 'field_agent_id');
    }

    /**
     * Get individual question answers for this response session.
     */
    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class);
    }
}
