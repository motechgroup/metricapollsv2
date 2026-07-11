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

    // Store the 3 random tests served for the session to prevent list jumping
    public $servedTestIds = [];

    public function mount()
    {
        $this->loadRandomTests();
    }

    public function loadRandomTests()
    {
        if (session()->has('served_test_ids')) {
            $this->servedTestIds = session('served_test_ids');
            $completedIds = DB::table('panelist_qualifications')
                ->where('user_id', auth()->id())
                ->pluck('qualification_test_id')
                ->toArray();
            
            $this->servedTestIds = array_values(array_diff($this->servedTestIds, $completedIds));
            
            if (count($this->servedTestIds) > 0) {
                return;
            }
        }

        $completedIds = DB::table('panelist_qualifications')
            ->where('user_id', auth()->id())
            ->pluck('qualification_test_id')
            ->toArray();

        $profile = PanelistProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            ['points_balance' => 0, 'experience_points' => 0, 'badge_level' => 'Bronze', 'is_verified' => false]
        );
        $myBadge = $profile->badge_level ?? 'Bronze';

        $allowedLevels = ['Level 1'];
        if ($myBadge === 'Silver') {
            $allowedLevels = ['Level 1', 'Level 2'];
        } elseif ($myBadge === 'Gold') {
            $allowedLevels = ['Level 1', 'Level 2', 'Level 3'];
        }

        $this->servedTestIds = QualificationTest::whereIn('level', $allowedLevels)
            ->whereNotIn('id', $completedIds)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('id')
            ->toArray();

        session(['served_test_ids' => $this->servedTestIds]);
    }

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
                'amount' => $this->activeTest->reward_points / 100,
                'points' => $this->activeTest->reward_points,
                'description' => 'Completed qualification: ' . $this->activeTest->title,
                'status' => 'completed',
            ]);

            session()->flash('success', "Qualification completed! You passed '{$this->activeTest->title}' and earned {$this->activeTest->reward_points} points.");

            // Clear session pool so a new pool is rolled
            session()->forget('served_test_ids');

            // Reset active test
            $this->activeTestId = null;
            $this->activeTest = null;
            $this->currentQuestionIndex = 0;
            $this->answers = [];

            // Reload fresh random uncompleted tests
            $this->loadRandomTests();
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
        // Query the currently served tests
        $tests = QualificationTest::whereIn('id', $this->servedTestIds)->get();

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
