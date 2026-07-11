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

    public $completedCourseIds = [];

    public function mount()
    {
        $this->loadCompletedCourses();
    }

    public function loadCompletedCourses()
    {
        // Load persistently from transactions table
        $this->completedCourseIds = Transaction::where('user_id', auth()->id())
            ->where('type', 'reward')
            ->where('description', 'like', 'Graduated Academy:%')
            ->get()
            ->map(function ($tx) {
                $title = str_replace('Graduated Academy: ', '', $tx->description);
                $course = AcademyCourse::where('title', $title)->first();
                return $course ? $course->id : null;
            })
            ->filter()
            ->unique()
            ->toArray();
    }

    public function selectCourse($id)
    {
        $this->activeCourseId = $id;
        $this->activeCourse = AcademyCourse::findOrFail($id);
        $this->currentLessonIndex = 0;
        
        // Start lesson timer to prevent fast-clicking fraud
        session(['lesson_start_time' => now()->timestamp]);
    }

    public function nextLesson()
    {
        // Validate minimum reading duration to prevent botting/cheating
        $startTime = session('lesson_start_time');
        $elapsed = $startTime ? (now()->timestamp - $startTime) : 0;

        if ($elapsed < 5) {
            $this->addError('speed', '⚠️ Integrity Trap: You are clicking through lessons too quickly. Please take at least 5 seconds to read and understand the material.');
            return;
        }

        $totalLessons = count($this->activeCourse->lessons);

        if ($this->currentLessonIndex + 1 < $totalLessons) {
            $this->currentLessonIndex++;
            // Reset timer for next lesson
            session(['lesson_start_time' => now()->timestamp]);
        } else {
            // Course Completed!
            $profile = PanelistProfile::firstOrCreate(
                ['user_id' => auth()->id()],
                ['points_balance' => 0, 'experience_points' => 0, 'badge_level' => 'Bronze', 'is_verified' => false]
            );

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

            session()->flash('success', "Congratulations! You completed '{$this->activeCourse->title}', earned {$this->activeCourse->points_award} points, {$this->activeCourse->points_award} EXP, and updated your badge level to {$newBadge}!");

            $this->activeCourseId = null;
            $this->activeCourse = null;
            $this->currentLessonIndex = 0;

            // Reload completed list
            $this->loadCompletedCourses();
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

            AcademyCourse::create([
                'title' => 'Public Opinion Polling & Neutrality Protocols',
                'description' => 'Learn the scientific methodologies for recording unbiased public opinions, demographic representation, and margin of error fundamentals.',
                'points_award' => 120,
                'lessons' => [
                    [
                        'title' => 'Avoiding Leading Questions',
                        'content' => 'Always read the opinion choices verbatim. Never inject tone emphasis, body language cues, or personal commentary that might skew a respondent towards a specific political or brand preference.'
                    ],
                    [
                        'title' => 'Understanding Demographic Quotas',
                        'content' => 'Surveys require specific proportions of age, gender, and regional cohorts. Adhering to these quotas ensures the statistical validity of public polls.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Offline Mapping & Rural Cohort Audits',
                'description' => 'Master offline database caching, synchronizing pending audits, and executing field mapping when research takes you off the grid.',
                'points_award' => 150,
                'lessons' => [
                    [
                        'title' => 'Offline Mode Operation',
                        'content' => 'When in remote rural tracts, turn on Metrica\'s offline database mode. Ensure your local browser storage compiles responses safely before re-connecting to mobile cellular networks.'
                    ],
                    [
                        'title' => 'Syncing Pending Audits',
                        'content' => 'Once you return to an active cellular data signal, trigger the manual upload sync immediately to prevent data collision or timestamp overlap with other field enumerators.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Mobile Money Agent & Financial Outlet Audits',
                'description' => 'How to conduct physical shop audits at Safaricom M-Pesa, Airtel Money, and bank agent booths to inspect liquid cash float and tariff transparency.',
                'points_award' => 200,
                'lessons' => [
                    [
                        'title' => 'Float Liquidity Checks',
                        'content' => 'When auditing financial agents, verify if they have sufficient cash float to process withdrawals up to 50,000 KES. Log actual cash vs digital float proportions accurately.'
                    ],
                    [
                        'title' => 'Tariff Poster Visibility',
                        'content' => 'Ensure the official tariff poster is clearly visible to customers. Take a geofenced photo of the tariff board to verify compliance with national financial regulatory guidelines.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Media & Audience Measurement Methodologies',
                'description' => 'Learn the core methodologies of media diaries, TV/radio rating points, digital audience audits, and out-of-home (OOH) billboards exposure.',
                'points_award' => 180,
                'lessons' => [
                    [
                        'title' => 'Media Diaries & Recall Logs',
                        'content' => 'When logging TV or Radio exposure, record the exact station name, program name, and timeframe (quarter-hour blocks). Avoid post-rationalization; only log actual live viewing or listening habits.'
                    ],
                    [
                        'title' => 'Digital Audience & Billboard Audits',
                        'content' => 'Digital audience measurement captures unique impressions, click-through rates, and average watch durations. For OOH (out-of-home) billboard campaigns, geolocated survey points evaluate pedestrian and vehicle traffic flow density to estimate brand reach.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Designing Effective Questionnaires & Survey Logic',
                'description' => 'Understand the fundamentals of questionnaire design, skip logic, piping answers, and matrix questions to avoid respondent fatigue.',
                'points_award' => 150,
                'lessons' => [
                    [
                        'title' => 'Skip Logic & Branching',
                        'content' => 'Skip logic dynamically hides or shows pages based on preceding answers. For instance, if a respondent does not drink coffee, the system automatically skips subsequent questions regarding favorite coffee bean brands.'
                    ],
                    [
                        'title' => 'Reducing Respondent Fatigue',
                        'content' => 'Avoid long matrix grids and repetitive rating scales. Keep prompt text concise and ensure multiple-choice lists include mutually exclusive options to keep response rates high.'
                    ]
                ]
            ]);

            AcademyCourse::create([
                'title' => 'Data Validation & Cross-Reference Checks',
                'description' => 'How research platforms audit data consistency across multiple variables (e.g., verifying age vs. year of birth and coordinate match checks).',
                'points_award' => 220,
                'lessons' => [
                    [
                        'title' => 'Logical Consistency Audits',
                        'content' => 'System algorithms scan for contradictions. Declaring yourself as a teenager while selecting \'Master\'s Degree\' or \'20+ years of work experience\' is automatically flagged by our data quality engine as inconsistent data.'
                    ],
                    [
                        'title' => 'Geolocation Verification Checks',
                        'content' => 'The audit dashboard cross-checks the registered mobile ISP location against physical device GPS coordinates. Discrepancies indicate proxy/VPN routing and result in survey rejection.'
                    ]
                ]
            ]);
        }

        $courses = AcademyCourse::all();

        // Get current user profile for badge information display
        $profile = PanelistProfile::where('user_id', auth()->id())->first();
        $badge = $profile ? ($profile->badge_level ?? 'Bronze') : 'Bronze';
        $exp = $profile ? ($profile->experience_points ?? 0) : 0;

        return view('PublicOpinion::livewire.academy', [
            'courses' => $courses,
            'userBadge' => $badge,
            'userExp' => $exp,
        ])->layout('Dashboard::panelist-portal');
    }
}
