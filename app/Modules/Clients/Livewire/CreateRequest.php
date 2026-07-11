<?php

namespace App\Modules\Clients\Livewire;

use Livewire\Component;
use App\Modules\Clients\Models\ResearchRequest;
use Livewire\Attributes\Title;

#[Title('Submit Research Brief - Metrica Polls')]
class CreateRequest extends Component
{
    public $title = '';
    public $description = '';
    public $target_audience = '';
    public $sample_size = 1000;
    public $estimated_budget = null;

    public function mount()
    {
        $serviceParam = request()->query('service');
        if ($serviceParam) {
            $this->title = $serviceParam . ' Campaign';
            $this->description = "We would like to request a quote for your " . $serviceParam . " service.\n\nHere are our objectives:\n1. \n2. \n\nTarget region:\nExpected timeline:\n";
        }
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'target_audience' => 'nullable|string|max:255',
        'sample_size' => 'required|integer|min:10',
        'estimated_budget' => 'nullable|numeric|min:0',
    ];

    public function submit()
    {
        $this->validate();

        ResearchRequest::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'target_audience' => $this->target_audience,
            'sample_size' => $this->sample_size,
            'estimated_budget' => $this->estimated_budget,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Your research request has been submitted successfully and is awaiting review.');

        return redirect()->route('client.requests');
    }

    public function render()
    {
        return view('Clients::livewire.create-request')
            ->layout('Dashboard::client-portal');
    }
}
