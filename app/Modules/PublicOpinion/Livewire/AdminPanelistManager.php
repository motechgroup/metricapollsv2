<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Livewire\Attributes\Title;

#[Title('Manage Panelists - Admin Portal')]
class AdminPanelistManager extends Component
{
    public $editingProfileId = null;
    public $editingProfile = null;

    // Form inputs for adjusting points and badge level
    public $badgeLevel = 'Bronze';
    public $pointsToAward = 0;
    public $pointsAwardReason = 'Manual adjustment by administrator';

    public function editPanelist($id)
    {
        $this->editingProfileId = $id;
        $profile = PanelistProfile::findOrFail($id);
        $this->editingProfile = $profile;
        $this->badgeLevel = $profile->badge_level ?? 'Bronze';
        $this->pointsToAward = 0;
        $this->pointsAwardReason = 'Manual adjustment by administrator';
    }

    public function closeEdit()
    {
        $this->editingProfileId = null;
        $this->editingProfile = null;
    }

    public function saveBadge()
    {
        $profile = PanelistProfile::findOrFail($this->editingProfileId);
        $profile->update([
            'badge_level' => $this->badgeLevel
        ]);

        session()->flash('success', "Updated badge level for {$profile->user->name} to {$this->badgeLevel}.");
        $this->closeEdit();
    }

    public function awardPoints()
    {
        $this->validate([
            'pointsToAward' => 'required|integer|min:-10000|max:10000',
            'pointsAwardReason' => 'required|string|max:255',
        ]);

        $profile = PanelistProfile::findOrFail($this->editingProfileId);
        
        // Adjust points
        $profile->increment('points_balance', $this->pointsToAward);

        // Record Transaction
        Transaction::create([
            'user_id' => $profile->user_id,
            'type' => $this->pointsToAward >= 0 ? 'reward' : 'withdrawal',
            'amount' => $this->pointsToAward / 100,
            'points' => $this->pointsToAward,
            'description' => $this->pointsAwardReason,
            'status' => 'completed',
        ]);

        session()->flash('success', "Awarded/Deducted {$this->pointsToAward} points to {$profile->user->name}.");
        $this->closeEdit();
    }

    public function toggleProfileVerification($id)
    {
        $profile = PanelistProfile::findOrFail($id);
        $profile->update([
            'is_verified' => !$profile->is_verified
        ]);

        session()->flash('success', "Toggled demographic profile verification for {$profile->user->name}.");
    }

    public function togglePhoneVerification($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'phone_verified' => !$user->phone_verified
        ]);

        session()->flash('success', "Toggled phone number verification for {$user->name}.");
    }

    public function render()
    {
        $profiles = PanelistProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PublicOpinion::livewire.admin-panelist-manager', [
            'profiles' => $profiles
        ])->layout('Dashboard::admin-layout');
    }
}
