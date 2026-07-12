<?php

namespace App\Modules\Wallet\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use App\Models\User;
use App\Services\GeoLocationService;
use Livewire\Attributes\Title;

#[Title('Panelist Dashboard & Verification - Metrica Polls')]
class PanelistDashboard extends Component
{
    // Demographic Profile fields
    public $gender = '';
    public $date_of_birth = '';
    public $education_level = '';
    public $income_bracket = '';
    public $location_region = '';
    public $is_verified = false;

    // Phone Verification fields
    public $phone = '';
    public $is_phone_verified = false;
    public $otpSent = false;
    public $otpCodeInput = '';

    // Dashboard Statistics
    public $points_balance = 0;
    public $experience_points = 0;
    public $badge_level = 'Bronze';
    public $completedSurveysCount = 0;
    public $availableSurveysCount = 0;

    // Geolocation fields
    public $detectedCountry = 'Kenya';
    public $mockCountry = 'Kenya';
    public $allowedCountries = ['Kenya', 'Rwanda', 'Tanzania', 'Uganda', 'Nigeria'];
    
    public $currencySymbol = 'KES';
    public $currencyCode = 'KES';
    public $exchangeRate = 100;

    protected $rules = [
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date|before:today',
        'education_level' => 'required|string',
        'income_bracket' => 'required|string',
        'location_region' => 'required|string',
    ];

    public function mount()
    {
        // Detect Geolocation Country
        $this->detectedCountry = GeoLocationService::getCountryFromIp(request()->ip());
        $this->mockCountry = session('mock_geo_country', $this->detectedCountry);

        // Fetch currency and conversion details
        $currency = GeoLocationService::getCurrencyForCountry($this->mockCountry);
        $this->currencySymbol = $currency['symbol'];
        $this->currencyCode = $currency['code'];
        $this->exchangeRate = $currency['rate'];

        $profile = PanelistProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            ['points_balance' => 0, 'experience_points' => 0, 'badge_level' => 'Bronze', 'is_verified' => false]
        );

        // Demographic Profile
        $this->gender = $profile->gender ?? '';
        $this->date_of_birth = $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '';
        $this->education_level = $profile->education_level ?? '';
        $this->income_bracket = $profile->income_bracket ?? '';
        $this->location_region = $profile->location_region ?? '';
        $this->is_verified = $profile->is_verified;

        // Phone Verification
        $this->phone = auth()->user()->phone ?? '';
        $this->is_phone_verified = auth()->user()->phone_verified ? true : false;

        // Statistics
        $this->points_balance = $profile->points_balance;
        $this->experience_points = $profile->experience_points;
        $this->badge_level = $profile->badge_level ?? 'Bronze';

        $this->completedSurveysCount = \App\Modules\SurveyEngine\Models\SurveyResponse::where('user_id', auth()->id())->count();

        // Check if allowed country, else block available surveys
        if (!GeoLocationService::isAllowedCountry($this->mockCountry)) {
            $this->availableSurveysCount = 0;
            return;
        }

        // Calculate available surveys count based on badge level requirements
        $myBadge = $this->badge_level;
        $badgeRank = ['Bronze' => 1, 'Silver' => 2, 'Gold' => 3];
        $myRank = $badgeRank[$myBadge] ?? 1;

        $takenSurveyIds = \App\Modules\SurveyEngine\Models\SurveyResponse::where('user_id', auth()->id())->pluck('survey_id')->toArray();

        $surveysQuery = \App\Modules\SurveyEngine\Models\Survey::where('status', 'published')
            ->where(function ($query) use ($myRank, $badgeRank) {
                foreach ($badgeRank as $bName => $bVal) {
                    if ($bVal > $myRank) {
                        $query->where('min_badge_level', '!=', $bName);
                    }
                }
            });

        if (\Illuminate\Support\Facades\Schema::hasColumn('surveys', 'target_country')) {
            $surveysQuery->where(function ($q) {
                $q->whereNull('target_country')->orWhere('target_country', $this->mockCountry);
            });
        }

        $this->availableSurveysCount = $surveysQuery->whereNotIn('id', $takenSurveyIds)->count();
    }

    public function updatedMockCountry($value)
    {
        session(['mock_geo_country' => $value]);
        return redirect()->route('dashboard.index');
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
            $profile->increment('points_balance', 100);
            $profile->increment('experience_points', 50); // Demographic EXP

            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'reward',
                'amount' => 1.00,
                'points' => 100,
                'description' => 'Demographic profiling verification bonus',
                'status' => 'completed',
            ]);

            $this->points_balance = $profile->points_balance;
            $this->experience_points = $profile->experience_points;
            $this->is_verified = true;

            // Trigger badge level recalculation
            $this->recalculateBadge($profile);

            session()->flash('success', 'Profile completed! You have been verified, awarded 100 points, and 50 EXP.');
        } else {
            session()->flash('success', 'Demographic profile updated successfully.');
        }
    }

    public function sendPhoneOtp()
    {
        $this->validate([
            'phone' => 'required|string|min:9',
        ]);

        $otpCode = strval(rand(100000, 999999));
        auth()->user()->update([
            'phone' => $this->phone,
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
            'phone_verified' => false,
        ]);

        try {
            $smsMsg = "Your Metrica Polls phone verification code is: {$otpCode}. Expires in 15 minutes.";
            \App\Services\TextSmsService::send($this->phone, $smsMsg);
        } catch (\Throwable $e) {
            logger("SMS dispatch failure: " . $e->getMessage());
        }

        logger("Phone verification OTP is: {$otpCode}");
        $this->otpSent = true;
        session()->flash('phone_status', "A verification OTP has been sent to your phone. (Demo code: {$otpCode})");
    }

    public function verifyPhoneOtp()
    {
        $this->validate([
            'otpCodeInput' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if (empty($user->otp_code) || $user->otp_code !== $this->otpCodeInput) {
            $this->addError('otpCodeInput', 'The verification code is incorrect.');
            return;
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            $this->addError('otpCodeInput', 'The verification code has expired.');
            return;
        }

        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'phone_verified' => true,
        ]);

        $this->is_phone_verified = true;
        $this->otpSent = false;
        $this->otpCodeInput = '';

        // Award verification EXP points
        $profile = PanelistProfile::where('user_id', auth()->id())->first();
        if ($profile) {
            $profile->increment('experience_points', 50); // 50 EXP for phone verification
            $this->experience_points = $profile->experience_points;
            $this->recalculateBadge($profile);
        }

        session()->flash('success', 'Your phone number has been verified successfully! You earned 50 EXP.');
    }

    private function recalculateBadge($profile)
    {
        $newBadge = 'Bronze';
        $exp = $profile->experience_points;
        if ($exp >= 300) {
            $newBadge = 'Gold';
        } elseif ($exp >= 100) {
            $newBadge = 'Silver';
        }

        if ($profile->badge_level !== $newBadge) {
            $profile->update(['badge_level' => $newBadge]);
            $this->badge_level = $newBadge;
        }
    }

    public function render()
    {
        return view('Wallet::livewire.panelist-dashboard')
            ->layout('Dashboard::panelist-portal');
    }
}
