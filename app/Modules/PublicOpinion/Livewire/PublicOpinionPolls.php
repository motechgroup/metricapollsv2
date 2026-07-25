<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
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

        if (!$poll->allow_public_voting) {
            session()->flash('error', 'Public voting is disabled for this poll.');
            return;
        }

        // Save Vote Record
        PublicOpinionVote::create([
            'public_opinion_id' => $pollId,
            'ip_address' => request()->ip(),
            'voted_option' => $optionName,
        ]);

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
        // Seed default dataset if missing
        if (PublicOpinion::count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'GeographicAndPoliticalSeeder']);
        }

        $query = PublicOpinion::with(['region', 'county', 'constituency']);

        if ($this->statusFilter === 'live') {
            $query->where('status', 'live');
        } elseif ($this->statusFilter === 'ended') {
            $query->where('status', 'ended');
        }

        if (!empty($this->selectedRegion)) {
            $query->where('region_id', $this->selectedRegion);
        }

        if (!empty($this->selectedCounty)) {
            $query->where('county_id', $this->selectedCounty);
        }

        if (!empty($this->selectedConstituency)) {
            $query->where('constituency_id', $this->selectedConstituency);
        }

        if (!empty($this->selectedPosition)) {
            $query->where('position_title', 'like', '%' . $this->selectedPosition . '%');
        }

        if (!empty($this->search)) {
            $query->where('topic', 'like', '%' . $this->search . '%');
        }

        $polls = $query->latest()->get();

        $activePollDetail = $this->selectedPollId ? PublicOpinion::with(['region', 'county', 'constituency'])->find($this->selectedPollId) : null;

        $regions = Region::orderBy('name')->get();
        $counties = County::orderBy('name')->get();
        $constituencies = $this->selectedCounty ? Constituency::where('county_id', $this->selectedCounty)->orderBy('name')->get() : collect();

        return view('PublicOpinion::livewire.public-opinion-polls', [
            'polls' => $polls,
            'activePollDetail' => $activePollDetail,
            'regions' => $regions,
            'counties' => $counties,
            'constituencies' => $constituencies,
        ])->layout('Corporate::layout');
    }
}
