<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\PublicOpinion\Models\PoliticalParty;
use Livewire\Attributes\Title;

#[Title('Manage Political Parties - Metrica Polls')]
class AdminPoliticalPartyManager extends Component
{
    use WithFileUploads;

    public $name = '';
    public $abbreviation = '';
    public $party_color = '#0A58CA';
    public $description = '';
    public $logo;
    public $editingPartyId = null;

    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'abbreviation' => 'nullable|string|max:50',
        'party_color' => 'required|string|max:20',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|max:2048',
    ];

    public function resetForm()
    {
        $this->name = '';
        $this->abbreviation = '';
        $this->party_color = '#0A58CA';
        $this->description = '';
        $this->logo = null;
        $this->editingPartyId = null;
    }

    public function saveParty()
    {
        $this->validate();

        $logoPath = null;
        if ($this->logo) {
            $filename = 'party_' . time() . '.' . $this->logo->getClientOriginalExtension();
            $this->logo->storeAs('public/images/parties', $filename);
            $logoPath = '/storage/images/parties/' . $filename;
        }

        if ($this->editingPartyId) {
            $party = PoliticalParty::findOrFail($this->editingPartyId);
            $data = [
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'party_color' => $this->party_color,
                'description' => $this->description,
            ];
            if ($logoPath) {
                $data['logo_path'] = $logoPath;
            }
            $party->update($data);
            session()->flash('success', 'Political Party updated successfully!');
        } else {
            PoliticalParty::create([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'party_color' => $this->party_color,
                'logo_path' => $logoPath ?: '/images/favicon.png',
                'description' => $this->description,
            ]);
            session()->flash('success', 'Political Party created successfully!');
        }

        $this->resetForm();
    }

    public function editParty($id)
    {
        $party = PoliticalParty::findOrFail($id);
        $this->editingPartyId = $party->id;
        $this->name = $party->name;
        $this->abbreviation = $party->abbreviation;
        $this->party_color = $party->party_color;
        $this->description = $party->description;
    }

    public function deleteParty($id)
    {
        PoliticalParty::destroy($id);
        session()->flash('success', 'Political Party deleted successfully.');
    }

    public function render()
    {
        $query = PoliticalParty::withCount('politicians');

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('abbreviation', 'like', '%' . $this->search . '%');
        }

        $parties = $query->latest()->get();

        return view('PublicOpinion::livewire.admin-political-party-manager', [
            'parties' => $parties,
        ])->layout('Dashboard::admin-layout');
    }
}
