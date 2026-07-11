<?php

namespace App\Modules\SurveyEngine\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Projects\Models\Project;

class Survey extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'settings',
        'is_paid',
        'payout_amount',
        'is_qualification',
        'min_badge_level',
        'target_country',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the project owning this survey.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the questions comprising this survey.
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get responses submitted for this survey.
     */
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
