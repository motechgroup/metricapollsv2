<?php

namespace App\Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Modules\Projects\Models\Project;

class ResearchRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_audience',
        'sample_size',
        'estimated_budget',
        'status',
    ];

    /**
     * Get the user representative who submitted the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project spawned from this approved request.
     */
    public function project()
    {
        return $this->hasOne(Project::class, 'research_request_id');
    }
}
