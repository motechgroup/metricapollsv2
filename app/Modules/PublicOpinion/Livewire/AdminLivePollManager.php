<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
use App\Modules\PublicOpinion\Models\Politician;
use App\Modules\PublicOpinion\Models\MediaAsset;
use Livewire\Attributes\Title;

#[Title('Live & Political Polls Manager - Metrica Polls')]
class AdminLivePollManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Editing & Creation state
    public $editingPollId = null;
    public $showEditModal = false;
    public $showCreateModal = false;

    // Media Manager / Gallery Modal state
    public $showMediaGalleryModal = false;
    public $mediaSearch = '';
    public $uploadedImage = null;
    public $targetCandidatePollId = null;
    public $targetCandidateIndex = null;
    public $targetFormType = null; // 'drawer', 'create_custom', 'edit_custom'

    // Form fields
    public $topic = '';
    public $target_level = 'county'; // national, region, county, constituency
    public $region_id = '';
    public $county_id = '';
    public $constituency_id = '';
    public $position_title = 'Woman Representative';
    public $status = 'live'; // live, ended
    public $allow_public_voting = false;
    public $expires_in_days = 14;

    // Selected Candidates & custom options with vote tallies
    public $selectedPoliticians = []; // array of ['politician_id' => X, 'votes' => Y]
    public $customOptions = []; // array of ['name' => X, 'photo' => Y, 'votes' => Z]

    // Filters & Pagination
    public $filterStatus = 'all'; // all, live, ended
    public $filterTargetLevel = 'all'; // all, national, region, county, constituency
    public $filterCountyId = '';
    public $searchQuery = '';
    public $perPage = 10;

    // Expandable Candidate Manager Drawer
    public $expandedPollId = null;

    protected $rules = [
        'topic' => 'required|string|max:255',
        'target_level' => 'required|string',
        'position_title' => 'required|string|max:255',
        'status' => 'required|string|in:live,ended',
        'allow_public_voting' => 'boolean',
    ];

    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTargetLevel()
    {
        $this->resetPage();
    }

    public function updatingFilterCountyId()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleExpandPoll($id)
    {
        $this->expandedPollId = $this->expandedPollId === $id ? null : $id;
    }

    public function updatedCountyId($value)
    {
        if ($value) {
            $county = County::find($value);
            if ($county) {
                $this->region_id = $county->region_id;
            }
        }
    }

    // Media Library Methods
    public function openMediaGallery($targetFormType = 'drawer', $pollId = null, $candidateIndex = null)
    {
        $this->targetFormType = $targetFormType;
        $this->targetCandidatePollId = $pollId;
        $this->targetCandidateIndex = $candidateIndex;
        $this->showMediaGalleryModal = true;

        // Auto seed media assets from existing politicians if empty
        if (MediaAsset::count() === 0) {
            $politicians = Politician::whereNotNull('photo_path')->get();
            foreach ($politicians as $pol) {
                MediaAsset::create([
                    'name' => $pol->name . ' Candidate Photo',
                    'file_path' => $pol->photo_path,
                    'category' => 'candidate_photo',
                ]);
            }
        }
    }

    public function closeMediaGallery()
    {
        $this->showMediaGalleryModal = false;
        $this->uploadedImage = null;
        $this->targetFormType = null;
        $this->targetCandidatePollId = null;
        $this->targetCandidateIndex = null;
    }

    public function uploadNewMediaAsset()
    {
        $this->validate([
            'uploadedImage' => 'required|image|max:5120', // Max 5MB
        ]);

        $path = $this->uploadedImage->store('candidate_media', 'public');
        $publicUrl = '/storage/' . $path;

        $asset = MediaAsset::create([
            'name' => $this->uploadedImage->getClientOriginalName(),
            'file_path' => $publicUrl,
            'category' => 'candidate_photo',
            'file_size' => $this->uploadedImage->getSize(),
        ]);

        session()->flash('success', 'Local image successfully uploaded to Media Library!');
        $this->selectMediaAsset($publicUrl);
    }

    public function selectMediaAsset($filePath)
    {
        if ($this->targetFormType === 'drawer' && $this->targetCandidatePollId && $this->targetCandidateIndex !== null) {
            $this->updateCandidatePhoto($this->targetCandidatePollId, $this->targetCandidateIndex, $filePath);
        } elseif ($this->targetFormType === 'custom' && $this->targetCandidateIndex !== null) {
            if (isset($this->customOptions[$this->targetCandidateIndex])) {
                $this->customOptions[$this->targetCandidateIndex]['photo'] = $filePath;
            }
        }

        $this->closeMediaGallery();
    }

    public function addPoliticianCandidate($politicianId, $votes = 0)
    {
        if (!$politicianId) return;

        // Check if already added
        foreach ($this->selectedPoliticians as $sp) {
            if ($sp['politician_id'] == $politicianId) {
                return;
            }
        }

        $this->selectedPoliticians[] = [
            'politician_id' => $politicianId,
            'votes' => (int) $votes,
        ];
    }

    public function removePoliticianCandidate($index)
    {
        unset($this->selectedPoliticians[$index]);
        $this->selectedPoliticians = array_values($this->selectedPoliticians);
    }

    public function addCustomOption()
    {
        $this->customOptions[] = [
            'name' => '',
            'photo' => '/images/favicon.png',
            'votes' => 0,
            'party_name' => 'OTHER',
            'party_color' => '#0A58CA'
        ];
    }

    public function removeCustomOption($index)
    {
        unset($this->customOptions[$index]);
        $this->customOptions = array_values($this->customOptions);
    }

    public function resetForm()
    {
        $this->editingPollId = null;
        $this->showEditModal = false;
        $this->showCreateModal = false;
        $this->topic = '';
        $this->target_level = 'county';
        $this->region_id = '';
        $this->county_id = '';
        $this->constituency_id = '';
        $this->position_title = 'Woman Representative';
        $this->status = 'live';
        $this->allow_public_voting = false;
        $this->selectedPoliticians = [];
        $this->customOptions = [];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($id)
    {
        $this->editPoll($id);
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function editPoll($id)
    {
        $poll = PublicOpinion::findOrFail($id);
        $this->editingPollId = $poll->id;
        $this->topic = $poll->topic;
        $this->target_level = $poll->target_level;
        $this->region_id = $poll->region_id ?: '';
        $this->county_id = $poll->county_id ?: '';
        $this->constituency_id = $poll->constituency_id ?: '';
        $this->position_title = $poll->position_title ?: 'Representative';
        $this->status = $poll->status;
        $this->allow_public_voting = (bool) $poll->allow_public_voting;

        $this->selectedPoliticians = [];
        $this->customOptions = [];

        if (is_array($poll->candidates_data)) {
            foreach ($poll->candidates_data as $cand) {
                if (!empty($cand['politician_id'])) {
                    $this->selectedPoliticians[] = [
                        'politician_id' => $cand['politician_id'],
                        'votes' => (int) ($cand['votes'] ?? 0),
                    ];
                } else {
                    $this->customOptions[] = [
                        'name' => $cand['name'] ?? '',
                        'photo' => $cand['photo'] ?? '/images/favicon.png',
                        'votes' => (int) ($cand['votes'] ?? 0),
                        'party_name' => $cand['party_name'] ?? 'OTHER',
                        'party_color' => $cand['party_color'] ?? '#0A58CA',
                    ];
                }
            }
        }
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function createPoll()
    {
        $this->validate();

        $candidatesData = [];
        $simpleOptions = [];
        $totalVotes = 0;

        // Build candidate options from selected politicians
        foreach ($this->selectedPoliticians as $item) {
            $pol = Politician::with('politicalParty')->find($item['politician_id']);
            if ($pol) {
                $votesCount = max(0, (int) ($item['votes'] ?? 0));
                $simpleOptions[] = $pol->name;
                $candidatesData[] = [
                    'name' => $pol->name,
                    'politician_id' => $pol->id,
                    'party_name' => $pol->politicalParty ? $pol->politicalParty->abbreviation : 'IND',
                    'party_color' => $pol->politicalParty ? $pol->politicalParty->party_color : '#6B7280',
                    'photo' => $pol->photo_path ?: '/images/favicon.png',
                    'votes' => $votesCount,
                ];
                $totalVotes += $votesCount;
            }
        }

        // Add custom text options if any
        foreach ($this->customOptions as $item) {
            if (isset($item['name']) && trim($item['name']) !== '') {
                $votesCount = max(0, (int) ($item['votes'] ?? 0));
                $optName = trim($item['name']);
                $simpleOptions[] = $optName;

                $photoPath = !empty($item['photo']) ? trim($item['photo']) : '/images/favicon.png';

                // Automatically index image into MediaAsset library for future reuse
                if ($photoPath && $photoPath !== '/images/favicon.png') {
                    MediaAsset::firstOrCreate([
                        'file_path' => $photoPath,
                    ], [
                        'name' => $optName . ' Candidate Photo',
                        'category' => 'candidate_photo',
                    ]);
                }

                $candidatesData[] = [
                    'name' => $optName,
                    'politician_id' => null,
                    'party_name' => $item['party_name'] ?? 'OTHER',
                    'party_color' => $item['party_color'] ?? '#0A58CA',
                    'photo' => $photoPath,
                    'votes' => $votesCount,
                ];
                $totalVotes += $votesCount;
            }
        }

        if (empty($simpleOptions)) {
            session()->flash('error', 'Please select at least one candidate politician or enter a custom candidate option.');
            return;
        }

        if ($this->editingPollId) {
            $poll = PublicOpinion::findOrFail($this->editingPollId);
            $poll->update([
                'topic' => $this->topic,
                'target_level' => $this->target_level,
                'region_id' => $this->region_id ?: null,
                'county_id' => $this->county_id ?: null,
                'constituency_id' => $this->constituency_id ?: null,
                'position_title' => $this->position_title,
                'options' => $simpleOptions,
                'candidates_data' => $candidatesData,
                'status' => $this->status,
                'allow_public_voting' => (bool) $this->allow_public_voting,
                'votes_count' => $totalVotes,
            ]);

            session()->flash('success', "Poll #{$poll->id} updated successfully!");
        } else {
            PublicOpinion::create([
                'topic' => $this->topic,
                'target_level' => $this->target_level,
                'region_id' => $this->region_id ?: null,
                'county_id' => $this->county_id ?: null,
                'constituency_id' => $this->constituency_id ?: null,
                'position_title' => $this->position_title,
                'options' => $simpleOptions,
                'candidates_data' => $candidatesData,
                'status' => $this->status,
                'allow_public_voting' => (bool) $this->allow_public_voting,
                'expires_at' => now()->addDays((int) $this->expires_in_days),
                'votes_count' => $totalVotes,
            ]);

            session()->flash('success', 'Live / Ended Political Poll successfully published!');
        }

        $this->resetForm();
    }

    public function updateCandidateVotes($pollId, $candidateIndex, $newVotes)
    {
        $poll = PublicOpinion::findOrFail($pollId);
        $candidates = $poll->candidates_data ?: [];

        if (isset($candidates[$candidateIndex])) {
            $candidates[$candidateIndex]['votes'] = max(0, (int) $newVotes);
            
            // Recalculate total votes count
            $newTotalVotes = 0;
            foreach ($candidates as $c) {
                $newTotalVotes += (int) ($c['votes'] ?? 0);
            }

            $poll->update([
                'candidates_data' => $candidates,
                'votes_count' => $newTotalVotes,
            ]);

            session()->flash('success', "Updated votes for candidate '{$candidates[$candidateIndex]['name']}'!");
        }
    }

    public function updateCandidatePhoto($pollId, $candidateIndex, $newPhoto)
    {
        $poll = PublicOpinion::findOrFail($pollId);
        $candidates = $poll->candidates_data ?: [];

        if (isset($candidates[$candidateIndex])) {
            $photoUrl = trim($newPhoto) ?: '/images/favicon.png';
            $candidates[$candidateIndex]['photo'] = $photoUrl;

            // Index into Media Library if new URL
            if ($photoUrl && $photoUrl !== '/images/favicon.png') {
                MediaAsset::firstOrCreate([
                    'file_path' => $photoUrl,
                ], [
                    'name' => $candidates[$candidateIndex]['name'] . ' Candidate Photo',
                    'category' => 'candidate_photo',
                ]);
            }

            $poll->update([
                'candidates_data' => $candidates,
            ]);

            session()->flash('success', "Updated photo for candidate '{$candidates[$candidateIndex]['name']}'!");
        }
    }

    public function togglePublicVoting($id)
    {
        $poll = PublicOpinion::findOrFail($id);
        $newVal = !$poll->allow_public_voting;
        $poll->update(['allow_public_voting' => $newVal]);

        $statusStr = $newVal ? 'ENABLED (Visitors can vote on site)' : 'DISABLED (Admin populates votes)';
        session()->flash('success', "Public voting for '{$poll->topic}' is now {$statusStr}.");
    }

    public function togglePollStatus($id)
    {
        $poll = PublicOpinion::findOrFail($id);
        $newStatus = $poll->status === 'live' ? 'ended' : 'live';
        $poll->update(['status' => $newStatus]);

        session()->flash('success', "Poll status changed to '{$newStatus}'.");
    }

    public function deletePoll($id)
    {
        PublicOpinion::destroy($id);
        if ($this->editingPollId == $id) {
            $this->resetForm();
        }
        session()->flash('success', 'Poll deleted successfully.');
    }

    public function render()
    {
        $regions = Region::orderBy('name')->get();
        $counties = County::orderBy('name')->get();
        $constituencies = $this->county_id ? Constituency::where('county_id', $this->county_id)->orderBy('name')->get() : collect();

        // Available politicians to select based on county / level filter
        $polQuery = Politician::with('politicalParty');
        if ($this->county_id) {
            $polQuery->where('county_id', $this->county_id);
        }
        $availablePoliticians = $polQuery->orderBy('name')->get();

        $pollsQuery = PublicOpinion::with(['region', 'county', 'constituency']);

        if ($this->filterStatus !== 'all') {
            $pollsQuery->where('status', $this->filterStatus);
        }

        if ($this->filterTargetLevel !== 'all') {
            $pollsQuery->where('target_level', $this->filterTargetLevel);
        }

        if ($this->filterCountyId) {
            $pollsQuery->where('county_id', $this->filterCountyId);
        }

        if (trim($this->searchQuery) !== '') {
            $q = trim($this->searchQuery);
            $pollsQuery->where(function($sub) use ($q) {
                $sub->where('topic', 'like', "%{$q}%")
                    ->orWhere('position_title', 'like', "%{$q}%");
            });
        }

        $polls = $pollsQuery->latest()->paginate((int) $this->perPage);

        // Fetch Media Assets for Media Gallery picker
        $mediaQuery = MediaAsset::latest();
        if (trim($this->mediaSearch) !== '') {
            $mQ = trim($this->mediaSearch);
            $mediaQuery->where('name', 'like', "%{$mQ}%")
                       ->orWhere('file_path', 'like', "%{$mQ}%");
        }
        $mediaAssets = $mediaQuery->get();

        return view('PublicOpinion::livewire.admin-live-poll-manager', [
            'regions' => $regions,
            'counties' => $counties,
            'constituencies' => $constituencies,
            'availablePoliticians' => $availablePoliticians,
            'polls' => $polls,
            'mediaAssets' => $mediaAssets,
        ])->layout('Dashboard::admin-layout');
    }
}
