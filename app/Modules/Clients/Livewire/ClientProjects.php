<?php

namespace App\Modules\Clients\Livewire;

use Livewire\Component;
use App\Modules\Projects\Models\Project;
use Livewire\Attributes\Title;

#[Title('Active Projects Tracker - Metrica Polls')]
class ClientProjects extends Component
{
    public function render()
    {
        $orgId = auth()->user()->client_organization_id;

        if ($orgId) {
            $projects = Project::where('client_organization_id', $orgId)
                ->with('projectManager')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $projects = collect();
        }

        return view('Clients::livewire.client-projects', [
            'projects' => $projects,
        ])->layout('Dashboard::client-portal');
    }
}
