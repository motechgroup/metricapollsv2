<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\Transaction;
use App\Modules\Wallet\Models\PanelistProfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomConfigurableMail;
use App\Services\TextSmsService;
use App\Models\Setting;
use Livewire\Attributes\Title;

#[Title('Manage Payouts - Admin Portal')]
class AdminPayoutManager extends Component
{
    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            session()->flash('error', 'This payout has already been processed.');
            return;
        }

        $transaction->update([
            'status' => 'completed'
        ]);

        // Dispatch notifications (Email & SMS)
        $kesAmount = abs($transaction->points);
        $user = $transaction->user;

        $phone = $user->phone ?? '';
        if (preg_match('/\(([^)]+)\)/', $transaction->description, $matches)) {
            $phone = $matches[1];
        }

        try {
            $subject = Setting::getValue('mail_template_payout_subject', 'M-Pesa Payout Completed - Metrica Polls');
            $body = Setting::getValue('mail_template_payout_body', '');
            Mail::to($user->email)->send(new CustomConfigurableMail($subject, $body, [
                'name' => $user->name,
                'amount' => number_format($kesAmount, 2),
                'phone' => $phone,
                'transaction_id' => $transaction->reference,
            ]));

            $smsTemplate = Setting::getValue('sms_template_payout', 'Metrica Polls: We have sent KES {amount} to your mobile money wallet. Ref: {ref}. Thank you for your research contributions!');
            $smsMsg = str_replace(['{amount}', '{ref}'], [number_format($kesAmount, 2), $transaction->reference], $smsTemplate);
            TextSmsService::send($phone, $smsMsg);
        } catch (\Throwable $e) {
            logger("Payout notification error: " . $e->getMessage());
        }

        session()->flash('success', "Payout reference {$transaction->reference} approved and notifications sent.");
    }

    public function reject($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            session()->flash('error', 'This payout has already been processed.');
            return;
        }

        $transaction->update([
            'status' => 'failed'
        ]);

        // Refund points back to user wallet profile
        $profile = PanelistProfile::where('user_id', $transaction->user_id)->first();
        if ($profile) {
            $profile->increment('points_balance', abs($transaction->points));
        }

        session()->flash('success', "Payout request {$transaction->reference} rejected. " . abs($transaction->points) . " points have been refunded to the user's wallet.");
    }

    public function render()
    {
        $payouts = Transaction::where('type', 'withdrawal')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PublicOpinion::livewire.admin-payout-manager', [
            'payouts' => $payouts
        ])->layout('Dashboard::admin-layout');
    }
}
