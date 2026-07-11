<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;

class Invoice extends Model
{
    protected $fillable = [
        'project_id',
        'client_organization_id',
        'invoice_number',
        'amount',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function clientOrganization()
    {
        return $this->belongsTo(ClientOrganization::class);
    }
}
