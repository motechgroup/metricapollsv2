<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\AdminPoll;
use Livewire\Attributes\Title;

#[Title('Manage AI Polls & Reports - Metrica Polls')]
class AdminPollManager extends Component
{
    public function togglePublic($id)
    {
        $poll = AdminPoll::findOrFail($id);
        $poll->update([
            'is_public' => !$poll->is_public
        ]);

        session()->flash('success', "Updated visibility for poll: '{$poll->title}'");
    }

    public function deletePoll($id)
    {
        $poll = AdminPoll::findOrFail($id);
        $poll->delete();

        session()->flash('success', "Deleted poll: '{$poll->title}' successfully.");
    }

    public function render()
    {
        $polls = AdminPoll::orderBy('created_at', 'desc')->get();

        return view('PublicOpinion::livewire.admin-poll-manager', [
            'polls' => $polls
        ])->layout('Dashboard::admin-layout');
    }
}
