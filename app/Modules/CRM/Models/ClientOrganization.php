<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Modules\Projects\Models\Project;

class ClientOrganization extends Model
{
    protected $fillable = [
        'name',
        'industry',
        'address',
        'website',
    ];

    /**
     * Get the client representatives (users linked to this organization).
     */
    public function representatives()
    {
        return $this->hasMany(User::class, 'client_organization_id');
    }

    /**
     * Get the projects spawned for this organization.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_organization_id');
    }
}
