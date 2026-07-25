<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use App\Modules\PublicOpinion\Models\PublicOpinionVote;
use App\Modules\PublicOpinion\Models\PublicOpinionComment;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class PublicOpinionPollDetail extends Component
{
    public $poll;
    public $votedPollIds = [];

    // Interactive UI states matching Politrack modern layout
    public $viewMode = 'list'; // list, grid
    public $trendTimeframe = '24H'; // 15M, 30M, 1H, 24H, 1W, 1M

    // Discussion comments
    public $newCommentAuthor = '';
    public $newCommentText = '';

    protected $rules = [
        'newCommentAuthor' => 'required|string|max:100',
        'newCommentText' => 'required|string|max:1000',
    ];

    public function mount($poll)
    {
        if ($poll instanceof PublicOpinion) {
            $this->poll = $poll->load(['region', 'county', 'constituency', 'comments']);
        } else {
            $this->poll = PublicOpinion::with(['region', 'county', 'constituency', 'comments'])->findOrFail($poll);
        }
        $this->votedPollIds = session()->get('voted_polls', []);

        // Anti-fraud check: Check persistent cookie or DB IP record on load
        $cookieName = "voted_opinion_poll_{$this->poll->id}";
        if (request()->hasCookie($cookieName) || PublicOpinionVote::where('public_opinion_id', $this->poll->id)->where('ip_address', request()->ip())->exists()) {
            if (!in_array($this->poll->id, $this->votedPollIds)) {
                $this->votedPollIds[] = $this->poll->id;
                session()->put('voted_polls', $this->votedPollIds);
            }
        }
    }

    public function setViewMode($mode)
    {
        if (in_array($mode, ['list', 'grid'])) {
            $this->viewMode = $mode;
        }
    }

    public function setTrendTimeframe($tf)
    {
        if (in_array($tf, ['15M', '30M', '1H', '24H', '1W', '1M'])) {
            $this->trendTimeframe = $tf;
        }
    }

    public function postComment()
    {
        $this->validate();

        PublicOpinionComment::create([
            'public_opinion_id' => $this->poll->id,
            'author_name' => $this->newCommentAuthor,
            'comment_text' => $this->newCommentText,
            'likes' => 0,
        ]);

        $this->newCommentAuthor = '';
        $this->newCommentText = '';
        $this->poll->refresh();

        session()->flash('comment_success', 'Your comment has been posted!');
    }

    public function likeComment($commentId)
    {
        $comment = PublicOpinionComment::findOrFail($commentId);
        $comment->increment('likes');
        $this->poll->refresh();
    }

    public function vote($optionName)
    {
        $ip = request()->ip();
        $cookieName = "voted_opinion_poll_{$this->poll->id}";

        // 1. Status & Configuration Check
        if ($this->poll->status !== 'live') {
            session()->flash('error', 'This poll has ended and is no longer accepting votes.');
            return;
        }

        if (!$this->poll->allow_public_voting) {
            session()->flash('error', 'Public voting is currently disabled for this poll.');
            return;
        }

        // 2. Anti-Fraud Layer A: Session Lock Check
        if (in_array($this->poll->id, $this->votedPollIds)) {
            session()->flash('error', 'Fraud Protection: You have already cast a vote in this poll session.');
            return;
        }

        // 3. Anti-Fraud Layer B: Persistent Cookie Fingerprint Check
        if (request()->hasCookie($cookieName)) {
            session()->flash('error', 'Fraud Protection: A vote from this browser/device has already been recorded.');
            if (!in_array($this->poll->id, $this->votedPollIds)) {
                $this->votedPollIds[] = $this->poll->id;
                session()->put('voted_polls', $this->votedPollIds);
            }
            return;
        }

        // 4. Anti-Fraud Layer C: Strict IP Address Duplicate Check
        $existingIpVote = PublicOpinionVote::where('public_opinion_id', $this->poll->id)
            ->where('ip_address', $ip)
            ->exists();

        if ($existingIpVote) {
            session()->flash('error', "Fraud Protection: IP address ({$ip}) has already recorded a vote for this poll.");
            if (!in_array($this->poll->id, $this->votedPollIds)) {
                $this->votedPollIds[] = $this->poll->id;
                session()->put('voted_polls', $this->votedPollIds);
            }
            return;
        }

        // 5. Database Transaction: Atomic vote increment + Audit trail record
        DB::transaction(function () use ($optionName, $ip) {
            // Record vote audit entry
            PublicOpinionVote::create([
                'public_opinion_id' => $this->poll->id,
                'ip_address' => $ip,
                'voted_option' => $optionName,
            ]);

            // Update candidate vote tally in candidates_data JSON array
            if (is_array($this->poll->candidates_data)) {
                $updatedCandidates = $this->poll->candidates_data;
                foreach ($updatedCandidates as &$cand) {
                    if ($cand['name'] === $optionName) {
                        $cand['votes'] = ($cand['votes'] ?? 0) + 1;
                        break;
                    }
                }
                $this->poll->candidates_data = $updatedCandidates;
            }

            $this->poll->increment('votes_count');
            $this->poll->save();
        });

        $this->poll->refresh();

        // 6. Lock Session & Set 30-day Persistent Cookie
        $this->votedPollIds[] = $this->poll->id;
        session()->put('voted_polls', $this->votedPollIds);
        Cookie::queue(cookie()->forever($cookieName, '1'));

        session()->flash('success', "✓ Vote registered for '{$optionName}'! Thank you for participating in this certified poll.");
    }

    public function render()
    {
        return view('PublicOpinion::livewire.public-opinion-poll-detail')
            ->layout('Corporate::layout')
            ->title(($this->poll->topic ?? 'Poll Details') . ' - Metrica Polls');
    }
}
