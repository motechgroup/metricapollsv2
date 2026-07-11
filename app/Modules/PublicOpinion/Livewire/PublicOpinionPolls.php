<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;
use Livewire\Attributes\Title;

#[Title('Public Opinion Polls - Metrica Polls')]
class PublicOpinionPolls extends Component
{
    // Tracks voted poll IDs in active session
    public $votedPollIds = [];

    public function mount()
    {
        $this->votedPollIds = session()->get('voted_polls', []);
    }

    public function vote($pollId, $option)
    {
        if (in_array($pollId, $this->votedPollIds)) {
            session()->flash('error', 'You have already voted in this opinion poll.');
            return;
        }

        $poll = PublicOpinion::findOrFail($pollId);

        // Save Vote
        PublicOpinionVote::create([
            'public_opinion_id' => $pollId,
            'ip_address' => request()->ip(),
            'voted_option' => $option,
        ]);

        // Update counts
        $poll->increment('votes_count');

        // Cache in session
        $this->votedPollIds[] = $pollId;
        session()->put('voted_polls', $this->votedPollIds);

        session()->flash('success', "Vote registered for option '{$option}'!");
    }

    public function render()
    {
        // Auto seed public polls if empty
        if (PublicOpinion::count() === 0) {
            PublicOpinion::create([
                'topic' => 'Preferred Mode of Daily Public Transport in Nairobi',
                'options' => ['Matatu', 'Boda Boda', 'Train/BRT', 'Personal Vehicle'],
                'status' => 'open',
                'votes_count' => 0,
            ]);

            PublicOpinion::create([
                'topic' => 'Primary Driver for FinTech Innovation in East Africa',
                'options' => ['Micro-loans / Digital lending', 'Mobile Wallets / M-Pesa', 'Cryptocurrency', 'Agency Banking'],
                'status' => 'open',
                'votes_count' => 0,
            ]);
        }

        $polls = PublicOpinion::all();
        
        $results = [];
        foreach ($polls as $poll) {
            $votes = PublicOpinionVote::where('public_opinion_id', $poll->id)->get();
            $optionCounts = array_fill_keys($poll->options, 0);

            foreach ($votes as $v) {
                if (isset($optionCounts[$v->voted_option])) {
                    $optionCounts[$v->voted_option]++;
                }
            }

            $optionsData = [];
            foreach ($optionCounts as $opt => $count) {
                $pct = $poll->votes_count > 0 ? ($count / $poll->votes_count) * 100 : 0;
                $optionsData[] = [
                    'option' => $opt,
                    'count' => $count,
                    'percentage' => round($pct, 1),
                ];
            }

            $results[$poll->id] = $optionsData;
        }

        return view('PublicOpinion::livewire.public-opinion-polls', [
            'polls' => $polls,
            'results' => $results,
        ])->layout('Corporate::layout');
    }
}
