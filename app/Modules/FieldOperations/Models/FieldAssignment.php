<?php

namespace App\Modules\FieldOperations\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Projects\Models\Project;
use App\Models\User;

class FieldAssignment extends Model
{
    protected $fillable = [
        'project_id',
        'field_agent_id',
        'target_submissions',
        'completed_submissions',
        'status',
    ];

    /**
     * Get the project associated with this field assignment.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the field agent assigned.
     */
    public function fieldAgent()
    {
        return $this->belongsTo(User::class, 'field_agent_id');
    }
}
