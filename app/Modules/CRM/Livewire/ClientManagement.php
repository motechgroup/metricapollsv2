<?php

namespace App\Modules\CRM\Livewire;

use Livewire\Component;
use App\Modules\CRM\Models\ClientOrganization;
use App\Models\User;
use App\Modules\Projects\Models\Project;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

#[Title('CRM Client Directory - Metrica Polls')]
class ClientManagement extends Component
{
    public $orgId = null;
    public $name = '';
    public $industry = '';
    public $address = '';
    public $website = '';

    public $isFormOpen = false;

    // Representative assignment
    public $selectedRepId = '';

    // Quick client representative creation
    public $newRepName = '';
    public $newRepEmail = '';

    // Project assignment
    public $selectedProjectId = '';

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->isFormOpen = true;

        if ($id) {
            $org = ClientOrganization::findOrFail($id);
            $this->orgId = $org->id;
            $this->name = $org->name;
            $this->industry = $org->industry;
            $this->address = $org->address;
            $this->website = $org->website;
        } else {
            $this->orgId = null;
            $this->name = '';
            $this->industry = '';
            $this->address = '';
            $this->website = '';
        }
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:client_organizations,name,' . ($this->orgId ?? 'NULL'),
            'industry' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        if ($this->orgId) {
            $org = ClientOrganization::findOrFail($this->orgId);
            $org->update([
                'name' => $this->name,
                'industry' => $this->industry,
                'address' => $this->address,
                'website' => $this->website,
            ]);
            session()->flash('success', "Organization '{$this->name}' updated successfully.");
        } else {
            ClientOrganization::create([
                'name' => $this->name,
                'industry' => $this->industry,
                'address' => $this->address,
                'website' => $this->website,
            ]);
            session()->flash('success', "New client organization '{$this->name}' registered.");
        }

        $this->closeForm();
    }

    public function assignRepresentative($orgId)
    {
        $this->validate([
            'selectedRepId' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($this->selectedRepId);
        $user->update([
            'client_organization_id' => $orgId,
        ]);

        $this->selectedRepId = '';
        session()->flash('success', "Assigned representative '{$user->name}' to organization.");
    }

    public function createAndLinkRepresentative($orgId)
    {
        $this->validate([
            'newRepName' => 'required|string|max:255',
            'newRepEmail' => 'required|email|max:255|unique:users,email',
        ]);

        // Create new user with Client role
        $user = User::create([
            'name' => $this->newRepName,
            'email' => $this->newRepEmail,
            'password' => Hash::make('Password@123'), // Default temporary password
            'client_organization_id' => $orgId,
        ]);

        // Assign Client role
        $user->assignRole('Client');

        $this->newRepName = '';
        $this->newRepEmail = '';

        session()->flash('success', "New representative account '{$user->name}' created and linked successfully.");
    }

    public function removeRepresentative($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'client_organization_id' => null,
        ]);
        session()->flash('success', "Removed representative access.");
    }

    public function assignProject($orgId)
    {
        $this->validate([
            'selectedProjectId' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($this->selectedProjectId);
        $project->update([
            'client_organization_id' => $orgId,
        ]);

        $this->selectedProjectId = '';
        session()->flash('success', "Project '{$project->name}' successfully assigned to organization.");
    }

    public function unlinkProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $project->update([
            'client_organization_id' => null,
        ]);
        session()->flash('success', "Unlinked project access.");
    }

    public function delete($id)
    {
        $org = ClientOrganization::findOrFail($id);
        $org->delete();
        session()->flash('success', 'Organization removed from directory.');
    }

    public function render()
    {
        $organizations = ClientOrganization::with(['representatives'])->get();

        // Eager load projects for each organization dynamically
        foreach ($organizations as $org) {
            $org->projects = Project::where('client_organization_id', $org->id)->get();
        }

        // Find users with 'Client' role that aren't assigned to any organization yet
        $availableRepresentatives = User::role('Client')
            ->whereNull('client_organization_id')
            ->get();

        // Find projects that are not assigned to any client organization yet
        $availableProjects = Project::whereNull('client_organization_id')->get();

        return view('CRM::livewire.client-management', [
            'organizations' => $organizations,
            'availableRepresentatives' => $availableRepresentatives,
            'availableProjects' => $availableProjects,
        ])->layout('Dashboard::admin-layout');
    }
}
