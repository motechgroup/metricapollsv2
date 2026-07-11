<?php

namespace App\Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\Clients\Models\ResearchRequest;

class Project extends Model
{
    protected $fillable = [
        'research_request_id',
        'client_organization_id',
        'name',
        'project_manager_id',
        'budget',
        'status',
        'target_quota',
        'current_responses',
        'start_date',
        'end_date',
    ];

    /**
     * Get the client organization owning this project.
     */
    public function clientOrganization()
    {
        return $this->belongsTo(ClientOrganization::class, 'client_organization_id');
    }

    /**
     * Get the project manager assigned to this project.
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Get the research request this project was spawned from.
     */
    public function researchRequest()
    {
        return $this->belongsTo(ResearchRequest::class, 'research_request_id');
    }

    /**
     * Get the survey associated with this project.
     */
    public function survey()
    {
        return $this->hasOne(\App\Modules\SurveyEngine\Models\Survey::class, 'project_id');
    }

    /**
     * Get quota completion percentage.
     */
    public function getProgressPercentAttribute()
    {
        if ($this->target_quota <= 0) {
            return 0;
        }

        return min(100, round(($this->current_responses / $this->target_quota) * 100));
    }
}
