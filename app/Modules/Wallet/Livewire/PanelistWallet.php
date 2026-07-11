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

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => -$usdAmount,
            'points' => -$this->pointsToRedeem,
            'description' => 'Redeemed ' . $this->pointsToRedeem . ' points to ' . str_replace('_', ' ', $this->payoutMethod) . ' (' . $this->phoneNumber . ')',
            'reference' => $mockRef,
            'status' => 'completed',
        ]);

        // Send notifications
        $kesAmount = $this->pointsToRedeem;
        $user = auth()->user();

        try {
            $subject = \App\Models\Setting::getValue('mail_template_payout_subject', 'M-Pesa Payout Initiated - Metrica Polls');
            $body = \App\Models\Setting::getValue('mail_template_payout_body', '');
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CustomConfigurableMail($subject, $body, [
                'name' => $user->name,
                'amount' => number_format($kesAmount, 2),
                'phone' => $this->phoneNumber,
                'transaction_id' => $mockRef,
            ]));

            $smsTemplate = \App\Models\Setting::getValue('sms_template_payout', 'Metrica Polls: We have sent KES {amount} to your M-Pesa wallet. Ref: {ref}. Thank you for your feedback!');
            $smsMsg = str_replace(['{amount}', '{ref}'], [number_format($kesAmount, 2), $mockRef], $smsTemplate);
            \App\Services\TextSmsService::send($this->phoneNumber, $smsMsg);
        } catch (\Throwable $e) {
            logger("Payout notification failure: " . $e->getMessage());
        }

        $this->phoneNumber = '';
        
        session()->flash('success', 'Redemption processed! $' . number_format($usdAmount, 2) . ' USD equivalent has been sent to ' . $mockRef);
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
