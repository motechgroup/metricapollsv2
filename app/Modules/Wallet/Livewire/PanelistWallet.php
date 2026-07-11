<?php

namespace App\Modules\Wallet\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use App\Services\GeoLocationService;
use Livewire\Attributes\Title;

#[Title('My Wallet & Earnings - Metrica Polls')]
class PanelistWallet extends Component
{
    public $pointsToRedeem = 500;
    public $payoutMethod = 'mobile_money';
    public $phoneNumber = '';

    // Geolocation details
    public $currencySymbol = 'KES';
    public $currencyCode = 'KES';
    public $exchangeRate = 100;
    public $payoutMethods = [];
    public $simulatedCountry = 'Kenya';

    public function mount()
    {
        $this->loadGeoDetails();
    }

    public function loadGeoDetails()
    {
        $this->simulatedCountry = session('mock_geo_country', GeoLocationService::getCountryFromIp(request()->ip()));
        $currency = GeoLocationService::getCurrencyForCountry($this->simulatedCountry);
        $this->currencySymbol = $currency['symbol'];
        $this->currencyCode = $currency['code'];
        $this->exchangeRate = $currency['rate'];
        $this->payoutMethods = GeoLocationService::getPayoutMethodsForCountry($this->simulatedCountry);

        // Reset payout method if current one is not supported in the simulated country
        if (!array_key_exists($this->payoutMethod, $this->payoutMethods)) {
            $this->payoutMethod = array_key_first($this->payoutMethods) ?: 'mobile_money';
        }
    }

    public function redeem()
    {
        $this->loadGeoDetails();
        
        $profile = PanelistProfile::where('user_id', auth()->id())->first();

        if (!$profile) {
            $this->addError('pointsToRedeem', 'Please complete your demographic profile first.');
            return;
        }

        $allowedPayouts = implode(',', array_keys($this->payoutMethods));

        $this->validate([
            'pointsToRedeem' => 'required|integer|min:100',
            'payoutMethod' => "required|in:{$allowedPayouts}",
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

        // Generate mock reference prefix based on country
        $refPrefix = 'TX';
        if ($this->payoutMethod === 'mobile_money') {
            $refPrefix = 'MOMO';
        } elseif ($this->payoutMethod === 'bank_transfer') {
            $refPrefix = 'BANK';
        } elseif ($this->payoutMethod === 'airtime') {
            $refPrefix = 'AIRTIME';
        }

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
        
        $localVal = number_format(($this->pointsToRedeem / 100) * ($this->exchangeRate), 2);
        
        session()->flash('success', "Redemption request submitted! Your request for {$this->currencySymbol} {$localVal} ({$this->currencyCode}) has been queued and is pending administrator review.");
    }

    public function render()
    {
        $this->loadGeoDetails();

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
