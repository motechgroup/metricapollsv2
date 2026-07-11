<?php

namespace App\Modules\Wallet\Livewire;

use Livewire\Component;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\QualificationTest;
use App\Modules\Wallet\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;

#[Title('Qualification Tests - Metrica Polls')]
class PanelistQualifications extends Component
{
    public $activeTestId = null;
    public $activeTest = null;
    public $currentQuestionIndex = 0;
    public $answers = [];

    public function selectTest($id)
    {
        $this->activeTestId = $id;
        $this->activeTest = QualificationTest::findOrFail($id);
        $this->currentQuestionIndex = 0;
        $this->answers = [];
    }

    public function answerQuestion($answer)
    {
        $this->answers[$this->currentQuestionIndex] = $answer;

        $totalQuestions = count($this->activeTest->questions);

        if ($this->currentQuestionIndex + 1 < $totalQuestions) {
            $this->currentQuestionIndex++;
        } else {
            // Test completed!
            $profile = PanelistProfile::where('user_id', auth()->id())->first();

            if (!$profile) {
                $profile = PanelistProfile::create([
                    'user_id' => auth()->id(),
                    'points_balance' => 0,
                    'is_verified' => false
                ]);
            }

            // Award points
            $profile->increment('points_balance', $this->activeTest->reward_points);

            // Log Qualification Record
            DB::table('panelist_qualifications')->insert([
                'user_id' => auth()->id(),
                'qualification_test_id' => $this->activeTest->id,
                'passed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'reward',
                'amount' => $this->activeTest->reward_points / 100, // $0.50 for 50 points
                'points' => $this->activeTest->reward_points,
                'description' => 'Completed qualification: ' . $this->activeTest->title,
                'status' => 'completed',
            ]);

            session()->flash('success', "Qualification completed! You passed '{$this->activeTest->title}' and earned {$this->activeTest->reward_points} points.");

            // Reset
            $this->activeTestId = null;
            $this->activeTest = null;
            $this->currentQuestionIndex = 0;
            $this->answers = [];
        }
    }

    public function cancelTest()
    {
        $this->activeTestId = null;
        $this->activeTest = null;
        $this->currentQuestionIndex = 0;
        $this->answers = [];
    }

    public function render()
    {
        // Auto-seed qualification tests if empty so the user has some to take right away!
        if (QualificationTest::count() === 0) {
            QualificationTest::create([
                'title' => 'Consumer Purchasing Habits Qualification',
                'description' => 'Qualify for high-budget retail research by detailing your monthly shopping cycles.',
                'reward_points' => 50,
                'questions' => [
                    [
                        'text' => 'Do you shop online at least once a month?',
                        'options' => ['Yes', 'No']
                    ],
                    [
                        'text' => 'What is your primary method of payment for retail purchases?',
                        'options' => ['Mobile Money (M-Pesa)', 'Credit/Debit Card', 'Cash']
                    ]
                ]
            ]);

            QualificationTest::create([
                'title' => 'Technology & Gadgets Adaptability Check',
                'description' => 'Qualify for smartphone and computing research panels by detailing your daily internet usage.',
                'reward_points' => 50,
                'questions' => [
                    [
                        'text' => 'Do you own a smartphone?',
                        'options' => ['Yes', 'No']
                    ],
                    [
                        'text' => 'How many hours do you spend online daily?',
                        'options' => ['Less than 2 hours', '2 to 5 hours', 'More than 5 hours']
                    ]
                ]
            ]);
        }

        // Fetch tests
        $tests = QualificationTest::all();

        // Fetch completed qualification IDs
        $completedIds = DB::table('panelist_qualifications')
            ->where('user_id', auth()->id())
            ->pluck('qualification_test_id')
            ->toArray();

        return view('Wallet::livewire.panelist-qualifications', [
            'tests' => $tests,
            'completedIds' => $completedIds,
        ])->layout('Dashboard::panelist-portal');
    }
}
