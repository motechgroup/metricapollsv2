<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Title;

#[Title('Live Polls & Results - Metrica Polls')]
class PublicOpinionPolls extends Component
{
    // Filter controls
    public $statusFilter = 'all'; // all, live, ended
    public $selectedRegion = '';
    public $selectedCounty = '';
    public $selectedConstituency = '';
    public $selectedPosition = '';
    public $search = '';

    // Selected poll for details modal
    public $selectedPollId = null;

    // Tracks voted poll IDs in active session
    public $votedPollIds = [];

    public function mount()
    {
        $this->votedPollIds = session()->get('voted_polls', []);
    }

    public function updatedSelectedCounty($value)
    {
        $this->selectedConstituency = '';
    }

    public function openPollDetails($pollId)
    {
        $this->selectedPollId = $pollId;
    }

    public function closePollDetails()
    {
        $this->selectedPollId = null;
    }

    public function vote($pollId, $optionName)
    {
        if (in_array($pollId, $this->votedPollIds)) {
            session()->flash('error', 'You have already voted in this poll.');
            return;
        }

        $poll = PublicOpinion::findOrFail($pollId);

        if ($poll->status !== 'live') {
            session()->flash('error', 'This poll has ended and is no longer accepting votes.');
            return;
        }

        if (Schema::hasColumn('public_opinions', 'allow_public_voting') && !$poll->allow_public_voting) {
            session()->flash('error', 'Public voting is disabled for this poll.');
            return;
        }

        // Save Vote Record if table exists
        if (Schema::hasTable('public_opinion_votes')) {
            PublicOpinionVote::create([
                'public_opinion_id' => $pollId,
                'ip_address' => request()->ip(),
                'voted_option' => $optionName,
            ]);
        }

        // Update counts in candidates_data JSON array if applicable
        if (is_array($poll->candidates_data)) {
            $updatedCandidates = $poll->candidates_data;
            foreach ($updatedCandidates as &$cand) {
                if ($cand['name'] === $optionName) {
                    $cand['votes'] = ($cand['votes'] ?? 0) + 1;
                    break;
                }
            }
            $poll->candidates_data = $updatedCandidates;
        }

        $poll->increment('votes_count');

        // Cache in session
        $this->votedPollIds[] = $pollId;
        session()->put('voted_polls', $this->votedPollIds);

        session()->flash('success', "Your vote for '{$optionName}' has been registered!");
    }

    public function render()
    {
        $query = PublicOpinion::query();

        // Safely check if geographic tables exist before eager loading
        if (Schema::hasTable('regions') && Schema::hasTable('counties') && Schema::hasTable('constituencies')) {
            $query->with(['region', 'county', 'constituency']);
        }

        if ($this->statusFilter === 'live') {
            $query->where('status', 'live');
        } elseif ($this->statusFilter === 'ended') {
            $query->where('status', 'ended');
        }

        if (!empty($this->selectedRegion) && Schema::hasColumn('public_opinions', 'region_id')) {
            $query->where('region_id', $this->selectedRegion);
        }

        if (!empty($this->selectedCounty) && Schema::hasColumn('public_opinions', 'county_id')) {
            $query->where('county_id', $this->selectedCounty);
        }

        if (!empty($this->selectedConstituency) && Schema::hasColumn('public_opinions', 'constituency_id')) {
            $query->where('constituency_id', $this->selectedConstituency);
        }

        if (!empty($this->selectedPosition) && Schema::hasColumn('public_opinions', 'position_title')) {
            $query->where('position_title', 'like', '%' . $this->selectedPosition . '%');
        }

        if (!empty($this->search)) {
            $query->where('topic', 'like', '%' . $this->search . '%');
        }

        $polls = $query->latest()->get();

        $activePollDetail = null;
        if ($this->selectedPollId) {
            $detailQuery = PublicOpinion::query();
            if (Schema::hasTable('regions')) {
                $detailQuery->with(['region', 'county', 'constituency']);
            }
            $activePollDetail = $detailQuery->find($this->selectedPollId);
        }

        $regions = Schema::hasTable('regions') ? Region::orderBy('name')->get() : collect();
        $counties = Schema::hasTable('counties') ? County::orderBy('name')->get() : collect();
        $constituencies = (Schema::hasTable('constituencies') && $this->selectedCounty) 
            ? Constituency::where('county_id', $this->selectedCounty)->orderBy('name')->get() 
            : collect();

        return view('PublicOpinion::livewire.public-opinion-polls', [
            'polls' => $polls,
            'activePollDetail' => $activePollDetail,
            'regions' => $regions,
            'counties' => $counties,
            'constituencies' => $constituencies,
        ])->layout('Corporate::layout');
    }
}
