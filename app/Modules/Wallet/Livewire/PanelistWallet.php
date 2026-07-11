<?php

namespace App\Modules\Wallet\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Livewire\Attributes\Title;

#[Title('My Wallet & Earnings - Metrica Polls')]
class PanelistWallet extends Component
{
    public $pointsToRedeem = 500;
    public $payoutMethod = 'mobile_money'; // mobile_money, airtime
    public $phoneNumber = '';

    public function redeem()
    {
        $profile = PanelistProfile::where('user_id', auth()->id())->first();

        if (!$profile) {
            $this->addError('pointsToRedeem', 'Please complete your demographic profile first.');
            return;
        }

        $this->validate([
            'pointsToRedeem' => 'required|integer|min:100',
            'payoutMethod' => 'required|in:mobile_money,airtime',
            'phoneNumber' => 'required|string|min:9',
        ]);

        if ($profile->points_balance < $this->pointsToRedeem) {
            $this->addError('pointsToRedeem', 'Insufficient points balance in your wallet.');
            return;
        }

        // Deduct points
        $profile->decrement('points_balance', $this->pointsToRedeem);

        // Map amount: 100 points = $1.00 USD
        $usdAmount = $this->pointsToRedeem / 100;

        // Generate mock reference
        $refPrefix = $this->payoutMethod === 'mobile_money' ? 'MPESA' : 'AIRTIME';
        $mockRef = $refPrefix . '-' . strtoupper(bin2hex(random_bytes(4)));

        // Create transaction as pending for admin approval
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => -$usdAmount,
            'points' => -$this->pointsToRedeem,
            'description' => 'Redeemed ' . $this->pointsToRedeem . ' points to ' . str_replace('_', ' ', $this->payoutMethod) . ' (' . $this->phoneNumber . ')',
            'reference' => $mockRef,
            'status' => 'pending',
        ]);

        $this->phoneNumber = '';
        
        session()->flash('success', 'Redemption request submitted! Your request for $' . number_format($usdAmount, 2) . ' USD has been queued and is pending administrator review and approval.');
    }

    public function render()
    {
        $profile = PanelistProfile::firstOrCreate(['user_id' => auth()->id()]);
        
        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Wallet::livewire.panelist-wallet', [
            'profile' => $profile,
            'transactions' => $transactions,
        ])->layout('Dashboard::panelist-portal');
    }
}
