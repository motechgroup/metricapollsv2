<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\AcademyCourse;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\Transaction;
use Livewire\Attributes\Title;

#[Title('Metrica Academy - Learn & Earn')]
class Academy extends Component
{
    public $activeCourseId = null;
    public $activeCourse = null;
    public $currentLessonIndex = 0;

    public $completedCourseIds = []; // Completed in active session or DB (we can track in session)

    public function selectCourse($id)
    {
        $this->activeCourseId = $id;
        $this->activeCourse = AcademyCourse::findOrFail($id);
        $this->currentLessonIndex = 0;
    }

    public function nextLesson()
    {
        $totalLessons = count($this->activeCourse->lessons);

        if ($this->currentLessonIndex + 1 < $totalLessons) {
            $this->currentLessonIndex++;
        } else {
            // Course Completed!
            $profile = PanelistProfile::where('user_id', auth()->id())->first();

            if (!$profile) {
                $profile = PanelistProfile::create([
                    'user_id' => auth()->id(),
                    'points_balance' => 0,
                    'experience_points' => 0,
                    'badge_level' => 'Bronze',
                    'is_verified' => false
                ]);
            }

            // Award points and experience points
            $profile->increment('points_balance', $this->activeCourse->points_award);
            $profile->increment('experience_points', $this->activeCourse->points_award);

            // Recalculate Badge Level (Bronze -> Silver -> Gold)
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

            // Log Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'reward',
                'amount' => $this->activeCourse->points_award / 100,
                'points' => $this->activeCourse->points_award,
                'description' => 'Graduated Academy: ' . $this->activeCourse->title,
                'status' => 'completed',
            ]);

            $this->completedCourseIds[] = $this->activeCourse->id;

            session()->flash('success', "Congratulations! You completed '{$this->activeCourse->title}' and earned {$this->activeCourse->points_award} points and {$this->activeCourse->points_award} EXP!");

            // Reset
            $this->activeCourseId = null;
            $this->activeCourse = null;
            $this->currentLessonIndex = 0;
        }
    }

    public function cancelCourse()
    {
        $this->activeCourseId = null;
        $this->activeCourse = null;
        $this->currentLessonIndex = 0;
    }

    public function render()
    {
        // Auto-seed academy courses if empty
        if (AcademyCourse::count() === 0) {
            AcademyCourse::create([
                'title' => 'Introduction to Survey Ethics',
                'description' => 'Learn core guidelines on informed consent, confidentiality, and voluntary respondent participation.',
                'points_award' => 50,
                'lessons' => [
                    [
                        'title' => 'Understanding Informed Consent',
                        'content' => 'Before starting any questionnaire, researchers must clearly communicate the scope, intent, and estimated time. Respondents must explicitly agree to proceed voluntarily, with the freedom to decline or skip any prompt at any time.'
                    ],
                    [
                        'title' => 'Respecting Respondent Anonymity',
                        'content' => 'Information captured during survey campaigns must be aggregated. Never share direct email, names, or location variables without explicit consent. Personal identifiers are protected by strict corporate data privacy rules.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Conducting Quality Field Interviews',
                'description' => 'Learn how to present yourself, establish rapport, and capture data neutrally during face-to-face assignments.',
                'points_award' => 50,
                'lessons' => [
                    [
                        'title' => 'Rapport and Neutrality',
                        'content' => 'Establish respect by introducing yourself and the organization clearly. Always read questions exactly as written, in a neutral tone, without prompting or influencing the respondent\'s choice in any direction.'
                    ],
                    [
                        'title' => 'Recording Observations Correctly',
                        'content' => 'Log answer inputs in real-time. In offline remote regions, ensure your offline device cache saves the data, capturing correct GPS tags for auditing before leaving the cohort area.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Advanced Data Auditing and GPS Traps',
                'description' => 'Learn about the advanced tracking technologies Metrica uses to verify respondent integrity, coordinate auditing, and timing traps.',
                'points_award' => 75,
                'lessons' => [
                    [
                        'title' => 'GPS Geofencing Auditing',
                        'content' => 'Metrica Polls matches respondent locations against target census tracts. Falsifying device coordinates using mock locations results in immediate account suspension.'
                    ],
                    [
                        'title' => 'Avoiding Time Traps',
                        'content' => 'Panelists must spend a minimum of 2 seconds per question. Reading questions fully ensures high-quality results for enterprise research clients.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'FMCG Brand Auditing Protocols',
                'description' => 'How to conduct product shelf monitoring and brand audit questionnaires in local retail stores.',
                'points_award' => 100,
                'lessons' => [
                    [
                        'title' => 'Brand Visibility Auditing',
                        'content' => 'When auditing FMCG visibility, count the number of facings on the primary shelf at eye level. Take clear photos of brand placement without violating store privacy policies.'
                    ],
                    [
                        'title' => 'Verification of Expiry & Pricing',
                        'content' => 'Always cross-reference product shelf price tags against printed receipt barcodes. Accurate pricing intelligence is vital for retail distribution studies.'
                    ]
                ]
            ]);
        }

        $courses = AcademyCourse::all();

        return view('PublicOpinion::livewire.academy', [
            'courses' => $courses,
        ])->layout('Dashboard::panelist-portal'); // Uses panelist layout since it is part of their learning
    }
}
