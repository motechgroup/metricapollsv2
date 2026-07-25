<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Modules\PublicOpinion\Models\Politician;
use App\Modules\PublicOpinion\Models\PoliticalParty;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
use App\Modules\PublicOpinion\Models\MediaAsset;
use Livewire\Attributes\Title;

#[Title('Manage Politicians & Candidates - Metrica Polls')]
class AdminPoliticianManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Form Fields
    public $name = '';
    public $photo;
    public $photo_url = '';
    public $political_party_id = '';
    public $level = 'county'; // national, region, county, constituency
    public $region_id = '';
    public $county_id = '';
    public $constituency_id = '';
    public $position_title = 'Governor';
    public $bio = '';

    public $editingPoliticianId = null;
    public $showModal = false;

    // Media Gallery Modal State
    public $showMediaGalleryModal = false;
    public $mediaSearch = '';
    public $uploadedGalleryImage = null;

    // Filters & Pagination
    public $search = '';
    public $filterParty = '';
    public $filterCounty = '';
    public $filterLevel = 'all';
    public $perPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255',
        'photo' => 'nullable|image|max:5120',
        'political_party_id' => 'nullable|exists:political_parties,id',
        'level' => 'required|string',
        'region_id' => 'nullable|exists:regions,id',
        'county_id' => 'nullable|exists:counties,id',
        'constituency_id' => 'nullable|exists:constituencies,id',
        'position_title' => 'required|string|max:255',
        'bio' => 'nullable|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterParty()
    {
        $this->resetPage();
    }

    public function updatingFilterCounty()
    {
        $this->resetPage();
    }

    public function updatingFilterLevel()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Media Gallery Methods
    public function openMediaGallery()
    {
        $this->showMediaGalleryModal = true;

        if (MediaAsset::count() === 0) {
            $politicians = Politician::whereNotNull('photo_path')->get();
            foreach ($politicians as $pol) {
                MediaAsset::create([
                    'name' => $pol->name . ' Headshot',
                    'file_path' => $pol->photo_path,
                    'category' => 'candidate_photo',
                ]);
            }
        }
    }

    public function closeMediaGallery()
    {
        $this->showMediaGalleryModal = false;
        $this->uploadedGalleryImage = null;
    }

    public function uploadNewMediaAsset()
    {
        $this->validate([
            'uploadedGalleryImage' => 'required|image|max:5120',
        ]);

        $path = $this->uploadedGalleryImage->store('candidate_media', 'public');
        $publicUrl = '/storage/' . $path;

        MediaAsset::create([
            'name' => $this->uploadedGalleryImage->getClientOriginalName(),
            'file_path' => $publicUrl,
            'category' => 'candidate_photo',
            'file_size' => $this->uploadedGalleryImage->getSize(),
        ]);

        session()->flash('success', 'Image uploaded to Media Library!');
        $this->selectMediaAsset($publicUrl);
    }

    public function selectMediaAsset($filePath)
    {
        $this->photo_url = $filePath;
        $this->closeMediaGallery();
    }

    public function openRegisterModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->editPolitician($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->photo = null;
        $this->photo_url = '';
        $this->political_party_id = '';
        $this->level = 'county';
        $this->region_id = '';
        $this->county_id = '';
        $this->constituency_id = '';
        $this->position_title = 'Governor';
        $this->bio = '';
        $this->editingPoliticianId = null;
    }

    public function updatedCountyId($value)
    {
        if ($value) {
            $county = County::find($value);
            if ($county) {
                $this->region_id = $county->region_id;
            }
        } else {
            $this->constituency_id = '';
        }
    }

    public function savePolitician()
    {
        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            $filename = 'politician_' . time() . '.' . $this->photo->getClientOriginalExtension();
            $path = $this->photo->storeAs('public/images/politicians', $filename);
            $photoPath = '/storage/images/politicians/' . $filename;

            // Save to Media Asset Library
            MediaAsset::create([
                'name' => $this->name . ' Candidate Headshot',
                'file_path' => $photoPath,
                'category' => 'candidate_photo',
            ]);
        } elseif (trim($this->photo_url) !== '') {
            $photoPath = trim($this->photo_url);

            // Index into Media Asset Library if new
            MediaAsset::firstOrCreate([
                'file_path' => $photoPath,
            ], [
                'name' => $this->name . ' Headshot',
                'category' => 'candidate_photo',
            ]);
        }

        $data = [
            'name' => $this->name,
            'political_party_id' => $this->political_party_id ?: null,
            'level' => $this->level,
            'region_id' => $this->region_id ?: null,
            'county_id' => $this->county_id ?: null,
            'constituency_id' => $this->constituency_id ?: null,
            'position_title' => $this->position_title,
            'bio' => $this->bio,
        ];

        if ($photoPath) {
            $data['photo_path'] = $photoPath;
        }

        if ($this->editingPoliticianId) {
            $politician = Politician::findOrFail($this->editingPoliticianId);
            $politician->update($data);
            session()->flash('success', 'Politician / Candidate profile updated successfully!');
        } else {
            if (!$photoPath) {
                $data['photo_path'] = '/images/favicon.png';
            }
            Politician::create($data);
            session()->flash('success', 'Politician / Candidate registered successfully!');
        }

        $this->closeModal();
    }

    public function editPolitician($id)
    {
        $politician = Politician::findOrFail($id);
        $this->editingPoliticianId = $politician->id;
        $this->name = $politician->name;
        $this->photo_url = $politician->photo_path ?: '';
        $this->political_party_id = $politician->political_party_id;
        $this->level = $politician->level;
        $this->region_id = $politician->region_id;
        $this->county_id = $politician->county_id;
        $this->constituency_id = $politician->constituency_id;
        $this->position_title = $politician->position_title;
        $this->bio = $politician->bio;
    }

    public function deletePolitician($id)
    {
        Politician::destroy($id);
        session()->flash('success', 'Politician profile deleted successfully.');
    }

    public function render()
    {
        $query = Politician::with(['politicalParty', 'region', 'county', 'constituency']);

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('position_title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterParty)) {
            $query->where('political_party_id', $this->filterParty);
        }

        if (!empty($this->filterCounty)) {
            $query->where('county_id', $this->filterCounty);
        }

        if ($this->filterLevel !== 'all') {
            $query->where('level', $this->filterLevel);
        }

        $politicians = $query->latest()->paginate((int) $this->perPage);

        $parties = PoliticalParty::orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $counties = County::orderBy('name')->get();
        $constituencies = $this->county_id ? Constituency::where('county_id', $this->county_id)->orderBy('name')->get() : collect();

        // Fetch Media Gallery Assets
        $mediaQuery = MediaAsset::latest();
        if (trim($this->mediaSearch) !== '') {
            $mQ = trim($this->mediaSearch);
            $mediaQuery->where('name', 'like', "%{$mQ}%")
                       ->orWhere('file_path', 'like', "%{$mQ}%");
        }
        $mediaAssets = $mediaQuery->get();

        return view('PublicOpinion::livewire.admin-politician-manager', [
            'politicians' => $politicians,
            'parties' => $parties,
            'regions' => $regions,
            'counties' => $counties,
            'constituencies' => $constituencies,
            'mediaAssets' => $mediaAssets,
        ])->layout('Dashboard::admin-layout');
    }
}
