<?php

namespace App\Modules\Projects\Livewire;

use Livewire\Component;
use App\Modules\Clients\Models\ResearchRequest;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;
use Livewire\Attributes\Title;

#[Title('Review Research Requests - Metrica Polls')]
class RequestReview extends Component
{
    public function approve($id)
    {
        $request = ResearchRequest::findOrFail($id);

        // Fetch client representative's organization
        $orgId = $request->user->client_organization_id;

        if (!$orgId) {
            // Fallback: If client doesn't have an organization linked yet, auto-create a personal org
            $org = ClientOrganization::create([
                'name' => $request->user->name . ' (Personal Account)',
            ]);
            $request->user->update([
                'client_organization_id' => $org->id,
            ]);
            $orgId = $org->id;
        }

        // Spawn a project
        Project::create([
            'research_request_id' => $request->id,
            'client_organization_id' => $orgId,
            'name' => $request->title,
            'budget' => $request->estimated_budget,
            'target_quota' => $request->sample_size,
            'status' => 'planning', // Spawn as planning
        ]);

        $request->update(['status' => 'approved']);

        session()->flash('success', "Research request '{$request->title}' approved. Project spawned.");
    }

    public function reject($id)
    {
        $request = ResearchRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        session()->flash('success', "Research request '{$request->title}' rejected.");
    }

    public function render()
    {
        // Get all pending requests, plus others for reference
        $requests = ResearchRequest::with('user.clientOrganization')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Projects::livewire.request-review', [
            'requests' => $requests,
        ])->layout('Dashboard::admin-layout');
    }
}
