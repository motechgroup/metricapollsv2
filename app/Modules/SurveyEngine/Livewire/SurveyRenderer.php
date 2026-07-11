<?php

namespace App\Modules\SurveyEngine\Livewire;

use Livewire\Component;
use App\Modules\SurveyEngine\Models\Survey;
use App\Modules\SurveyEngine\Models\Question;
use App\Modules\SurveyEngine\Models\SurveyResponse;
use App\Modules\SurveyEngine\Models\ResponseAnswer;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;

#[Title('Respond to Survey - Metrica Polls')]
class SurveyRenderer extends Component
{
    public $surveyId;
    public $survey;

    // Holds answer inputs mapped by question_id
    public $answers = [];

    public $isSubmitted = false;

    // Fraud timing trap anchor
    public $startTime;

    public $isFraudDetected = false;
    public $fraudReasonDetail = '';

    public function mount($surveyId)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in via Google to access and complete online training and surveys.');
        }

        $this->surveyId = $surveyId;
        $this->survey = Survey::with('questions')->findOrFail($surveyId);

        // Capture session start timestamp
        $this->startTime = time();

        // Pre-populate answers array
        foreach ($this->survey->questions as $q) {
            if ($q->type === 'multiple_choice') {
                $this->answers[$q->id] = [];
            } else {
                $this->answers[$q->id] = '';
            }
        }
    }

    public function submit()
    {
        // Dynamic Validation
        $rules = [];
        $messages = [];

        foreach ($this->survey->questions as $q) {
            if ($q->type === 'multiple_choice') {
                $rules["answers.{$q->id}"] = 'required|array|min:1';
                $messages["answers.{$q->id}.required"] = "The question '{$q->question_text}' requires at least one option selected.";
            } else {
                $rules["answers.{$q->id}"] = 'required|string';
                $messages["answers.{$q->id}.required"] = "The question '{$q->question_text}' is required.";
            }
        }

        $this->validate($rules, $messages);

        // --- ANTI-FRAUD SECURITY ENGINE ---
        $durationSeconds = time() - $this->startTime;
        $isFraudulent = false;
        $fraudReason = null;

        // 1. Timing Speed Trap (suspect completions under 2 seconds per question)
        $minRequiredSeconds = count($this->survey->questions) * 2;
        if ($durationSeconds < $minRequiredSeconds) {
            $isFraudulent = true;
            $fraudReason = "Timing Trap: Survey completed in {$durationSeconds}s, which is below the human safety threshold of {$minRequiredSeconds}s.";
        }

        // 2. Attention Check Questions Verification
        foreach ($this->survey->questions as $q) {
            $answerVal = $this->answers[$q->id];
            
            // Format check: "(Attention check: Select OptionName)"
            if (stripos($q->question_text, '(Attention check: Select') !== false) {
                preg_match('/\(Attention check: Select ([^\)]+)\)/i', $q->question_text, $matches);
                if (isset($matches[1])) {
                    $expected = trim($matches[1]);
                    $selected = is_array($answerVal) ? ($answerVal[0] ?? '') : $answerVal;
                    
                    if (strcasecmp(trim($selected), $expected) !== 0) {
                        $isFraudulent = true;
                        $fraudReason = "Attention Check Failure: User selected '{$selected}' instead of the requested verification answer '{$expected}'.";
                    }
                }
            }
        }

        // 3. Double Submission check (IP/User duplicate)
        if (auth()->check()) {
            $duplicate = SurveyResponse::where('survey_id', $this->survey->id)
                ->where('user_id', auth()->id())
                ->where('status', 'completed')
                ->where('is_fraudulent', false)
                ->exists();

            if ($duplicate) {
                $isFraudulent = true;
                $fraudReason = "Duplicate Submission: User has already submitted a response for this survey.";
            }
        }

        // --- CREATE RESPONSE RECORD ---
        $response = SurveyResponse::create([
            'survey_id' => $this->survey->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'gps_latitude' => -1.2921,
            'gps_longitude' => 36.8219,
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => $durationSeconds,
            'is_fraudulent' => $isFraudulent,
            'fraud_reason' => $fraudReason,
        ]);

        // Save individual Answers
        foreach ($this->survey->questions as $q) {
            $answerVal = $this->answers[$q->id];

            if (is_array($answerVal)) {
                $answerVal = json_encode($answerVal);
            }

            ResponseAnswer::create([
                'survey_response_id' => $response->id,
                'question_id' => $q->id,
                'answer_value' => strval($answerVal),
            ]);
        }

        // --- REWARD & BADGE PIPELINE (ONLY FOR VALID RESPONSES) ---
        if (auth()->check() && !$isFraudulent) {
            $profile = PanelistProfile::firstOrCreate(
                ['user_id' => auth()->id()],
                ['points_balance' => 0, 'experience_points' => 0, 'badge_level' => 'Bronze']
            );

            // A. Qualification / Training complete
            if ($this->survey->is_qualification) {
                $profile->update(['is_verified' => true]);
                $profile->increment('points_balance', 150);
                $profile->increment('experience_points', 50);

                Transaction::create([
                    'user_id' => auth()->id(),
                    'type' => 'reward',
                    'amount' => 1.50,
                    'points' => 150,
                    'description' => 'Successfully passed qualification test and training survey',
                    'status' => 'completed',
                ]);
            }

            // B. Paid Survey Reward
            if ($this->survey->is_paid) {
                $payout = $this->survey->payout_amount;
                $points = intval($payout); // 1 point = 1 KES
                
                $profile->increment('points_balance', $points);
                $profile->increment('experience_points', 30);

                Transaction::create([
                    'user_id' => auth()->id(),
                    'type' => 'reward',
                    'amount' => $payout / 100, // Equiv USD
                    'points' => $points,
                    'description' => 'Paid Survey Completion: ' . $this->survey->title,
                    'status' => 'completed',
                ]);
            }

            // C. Dynamic Badge Level Upgrades (Bronze -> Silver -> Gold)
            $newBadge = 'Bronze';
            $exp = $profile->experience_points;
            if ($exp >= 300) {
                $newBadge = 'Gold';
            } elseif ($exp >= 100) {
                $newBadge = 'Silver';
            }

            if ($profile->badge_level !== $newBadge) {
                $profile->update(['badge_level' => $newBadge]);
            }
        }

        $this->isSubmitted = true;
        $this->isFraudDetected = $isFraudulent;
        $this->fraudReasonDetail = $fraudReason;

        if ($isFraudulent) {
            session()->flash('error', 'Security Warning: Our automated anti-fraud engine flagged this submission. No rewards will be disbursed.');
        } else {
            session()->flash('success', 'Thank you! Your responses have been verified, and rewards added to your wallet.');
        }
    }

    public function render()
    {
        return view('SurveyEngine::livewire.survey-renderer')
            ->layout('Corporate::layout');
    }
}
