<?php

namespace App\Modules\Clients\Livewire;

use Livewire\Component;
use App\Modules\Clients\Models\ResearchRequest;
use Livewire\Attributes\Title;

#[Title('My Research Requests - Metrica Polls')]
class ClientRequests extends Component
{
    public function render()
    {
        $requests = ResearchRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Clients::livewire.client-requests', [
            'requests' => $requests,
        ])->layout('Dashboard::client-portal');
    }
}
