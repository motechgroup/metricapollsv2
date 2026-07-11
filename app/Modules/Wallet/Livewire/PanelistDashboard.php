<?php

namespace App\Modules\Wallet\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Livewire\Attributes\Title;

#[Title('Panelist Profile - Metrica Polls')]
class PanelistDashboard extends Component
{
    public $gender = '';
    public $date_of_birth = '';
    public $education_level = '';
    public $income_bracket = '';
    public $location_region = '';
    public $is_verified = false;
    public $points_balance = 0;

    protected $rules = [
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date|before:today',
        'education_level' => 'required|string',
        'income_bracket' => 'required|string',
        'location_region' => 'required|string',
    ];

    public function mount()
    {
        $profile = PanelistProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            ['points_balance' => 0, 'is_verified' => false]
        );

        $this->gender = $profile->gender ?? '';
        $this->date_of_birth = $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '';
        $this->education_level = $profile->education_level ?? '';
        $this->income_bracket = $profile->income_bracket ?? '';
        $this->location_region = $profile->location_region ?? '';
        $this->is_verified = $profile->is_verified;
        $this->points_balance = $profile->points_balance;
    }

    public function saveProfile()
    {
        $this->validate();

        $profile = PanelistProfile::where('user_id', auth()->id())->first();

        $firstTimeVerification = !$profile->is_verified;

        $profile->update([
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'education_level' => $this->education_level,
            'income_bracket' => $this->income_bracket,
            'location_region' => $this->location_region,
            'is_verified' => true,
        ]);

        if ($firstTimeVerification) {
            // Award profile completion bonus
            $profile->increment('points_balance', 100);

            // Log Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'reward',
                'amount' => 1.00, // Equivalent value e.g. $1.00
                'points' => 100,
                'description' => 'Demographic profiling verification bonus',
                'status' => 'completed',
            ]);

            $this->points_balance = $profile->points_balance;
            $this->is_verified = true;
            session()->flash('success', 'Profile completed! You have been verified and awarded 100 points.');
        } else {
            session()->flash('success', 'Demographic profile updated successfully.');
        }
    }

    public function render()
    {
        return view('Wallet::livewire.panelist-dashboard')
            ->layout('Dashboard::panelist-portal');
    }
}
