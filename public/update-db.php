<?php

// Prevent execution if not authorized
if (isset($_GET['key']) && $_GET['key'] !== 'metrica_update_2026') {
    die('Unauthorized access key.');
}

if (!isset($_GET['key'])) {
    echo "<h1>Database Update tool</h1>";
    echo "<p>Please visit this URL with the correct security key to run updates, for example:</p>";
    echo "<code>" . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?key=metrica_update_2026</code>";
    exit;
}

define('LARAVEL_START', microtime(true));

// Locate vendor autoload
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die('Autoload file not found. Make sure vendor folder exists.');
}

// Boot Laravel Application
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

try {
    // Run migrations programmatically
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();

    // Seed new surveys
    $org = \App\Modules\CRM\Models\ClientOrganization::firstOrCreate(['name' => 'Default Organization']);
    $project = \App\Modules\Projects\Models\Project::firstOrCreate(
        ['name' => 'General Opinion Index'],
        [
            'client_organization_id' => $org->id,
            'status' => 'live',
            'target_quota' => 1000,
            'current_responses' => 12,
        ]
    );

    // Survey 5
    $survey5 = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'Consumer Retail & FMCG Buying Habits 2026'],
        [
            'project_id' => $project->id,
            'description' => 'An FMCG research campaign auditing supermarket product selections, laundry detergent brands, and cooking oil brand loyalties across households.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 450.00,
            'min_badge_level' => 'Silver',
        ]
    );
    $survey5->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey5->id,
        'type' => 'single_choice',
        'question_text' => 'How often do you shop for household groceries?',
        'options' => ['Daily', 'Weekly', 'Monthly', 'Rarely'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey5->id,
        'type' => 'single_choice',
        'question_text' => 'Confirm you are human. (Attention check: Select Weekly)',
        'options' => ['Daily', 'Weekly', 'Monthly', 'Rarely'],
        'sort_order' => 2,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey5->id,
        'type' => 'single_choice',
        'question_text' => 'Which factor is most important when choosing a cooking oil brand?',
        'options' => ['Price', 'Quality', 'Brand Trust', 'Health benefits'],
        'sort_order' => 3,
    ]);

    // Survey 6
    $survey6 = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'East African Telco & Internet Connectivity Census'],
        [
            'project_id' => $project->id,
            'description' => 'A comprehensive study on cellular data packages, fibre internet reliability, and customer service satisfaction ratings.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 600.00,
            'min_badge_level' => 'Gold',
        ]
    );
    $survey6->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey6->id,
        'type' => 'single_choice',
        'question_text' => 'What is your primary home internet connection method?',
        'options' => ['Mobile Data hotspot', 'Fibre-to-the-Home', 'Fixed LTE', 'No Internet'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey6->id,
        'type' => 'single_choice',
        'question_text' => 'To prove attention, choose Fibre-to-the-Home. (Attention check: Select Fibre-to-the-Home)',
        'options' => ['Mobile Data hotspot', 'Fibre-to-the-Home', 'Fixed LTE', 'No Internet'],
        'sort_order' => 2,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $survey6->id,
        'type' => 'single_choice',
        'question_text' => 'How would you rate your telco\'s customer care responsiveness?',
        'options' => ['Excellent', 'Good', 'Average', 'Poor'],
        'sort_order' => 3,
    ]);

    // Seed new Academy Courses
    \App\Modules\PublicOpinion\Models\AcademyCourse::updateOrCreate(
        ['title' => 'Advanced Data Auditing and GPS Traps'],
        [
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
        ]
    );

    \App\Modules\PublicOpinion\Models\AcademyCourse::updateOrCreate(
        ['title' => 'FMCG Brand Auditing Protocols'],
        [
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
        ]
    );

    // Seed 150 random/predictability-avoiding qualification tests if they don't exist yet
    if (\App\Modules\Wallet\Models\QualificationTest::count() < 150) {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Modules\Wallet\Models\QualificationTest::truncate();
        \Illuminate\Support\Facades\DB::table('panelist_qualifications')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $categories = [
            'Consumer Retail' => [
                'Grocery frequency', 'Supermarket choice', 'Online shopping habits', 'Delivery service preferences', 'Laundry detergent choices',
                'Breakfast cereal preferences', 'Fast food consumption', 'Beverage choices', 'Apparel budget', 'Cosmetics brand trust',
                'Home appliance purchases', 'Organic food interest', 'Coffee shop loyalty', 'Local market vs mall shopping', 'Shoe brand preferences',
                'Bulk buying habits', 'Discount store usage', 'E-commerce platform trust', 'Eco-friendly product choices', 'Pet food brands',
                'Snack consumption frequency', 'Subscription box interest', 'Dairy product consumption', 'Frozen food preferences', 'Kitchen utensil selections',
                'Gift card usage', 'Secondhand shopping', 'Luxury item purchases', 'Energy drink consumption', 'Baking ingredient buying'
            ],
            'Finance & Banking' => [
                'Mobile money usage', 'Primary bank account choice', 'Savings frequency', 'Budgeting habits', 'Credit card preferences',
                'Microfinance loans', 'Insurance policy coverage', 'Investment portfolios', 'Cash vs cashless payments', 'Digital banking app satisfaction',
                'Personal loan usage', 'Fixed deposit interest', 'Stock market trading', 'Cryptocurrency familiarity', 'Pension contribution planning',
                'Tax filing methods', 'Peer-to-peer lending', 'Financial literacy courses', 'Emergency fund sizing', 'Mobile loan apps usage',
                'Real estate investment views', 'Family budget pooling', 'Gold and commodity savings', 'Foreign exchange transactions', 'Student loan repayment views',
                'Chama/investment group participation', 'Mobile banking security views', 'Remittance receipt frequency', 'Contactless card usage', 'Wealth advisory trust'
            ],
            'Technology & Media' => [
                'Smartphone brand preferences', 'Home internet speeds', 'Streaming services usage', 'Social media screen time', 'Gaming platform choices',
                'Smart home device setups', 'Podcast listening frequency', 'Online news portals trust', 'Privacy settings awareness', 'Wearable fitness monitors',
                'Virtual reality interest', 'Audiobook consumption', 'Ad blocker usage', 'Software subscription budgets', 'Laptop vs tablet primary usage',
                'Cloud storage preferences', 'AI tools productivity use', 'Smart TV brand satisfaction', 'Cybersecurity tools installation', 'Newsletter subscription frequency',
                'Dual SIM card configuration', 'Mobile app download frequency', 'Video conferencing preferences', 'Keyboard & mouse preferences', 'E-reader usage',
                'Bluetooth accessory brand trust', 'Online forum participation', 'Tech review site trust', 'VPN service subscriptions', 'Coding and tech education interest'
            ],
            'Travel & Commuting' => [
                'Daily commute methods', 'Ride-sharing app usage', 'Public transport reliability', 'Road trip frequency', 'Hotel booking preferences',
                'Flight booking loyalty', 'Electric vehicle perception', 'Bicycle commuting frequency', 'Luggage brand choices', 'Holiday destination criteria',
                'Airline ticket budget', 'Airport lounge access', 'Car rental frequency', 'Weekend getaway planning', 'Train travel experiences',
                'Ride-share driver rating priority', 'Fuel efficiency tracking', 'Map navigation app choice', 'Backpacking vs luxury travel', 'Foreign travel frequency',
                'Local tourism spots visited', 'Motorcycle transport usage', 'Carpooling arrangements', 'Commute duration tolerance', 'Travel insurance purchases',
                'Cruise line interest', 'Historical site visiting frequency', 'Camping equipment ownership', 'Urban walking habits', 'Toll road usage frequency'
            ],
            'Healthcare & Lifestyle' => [
                'Gym membership status', 'Daily hydration targets', 'Dietary preferences', 'Vitamins & supplements usage', 'Sleep tracking habits',
                'Coffee intake frequency', 'Home cooking habits', 'Outdoor activity preferences', 'Mental health breaks', 'Fast food frequency',
                'Water filter usage', 'Dental hygiene routines', 'Medication adherence views', 'Herbal remedies trust', 'Stress relief techniques',
                'Morning routine habits', 'Therapeutic massage interest', 'Vegan alternative trials', 'Smoking/vaping history', 'Alcohol brand choices',
                'Chronic illness tracking', 'Allergy management products', 'First aid kit readiness', 'Eco-friendly cleaning supplies', 'Yoga/mindfulness frequency',
                'Running/jogging weekly miles', 'Skin care routine steps', 'Eye health checks frequency', 'Family health history awareness', 'Organic farming interest'
            ]
        ];

        $testId = 1;
        foreach ($categories as $catName => $subCats) {
            foreach ($subCats as $index => $subCat) {
                if ($testId <= 50) {
                    $level = 'Level 1';
                    $points = 50;
                } elseif ($testId <= 100) {
                    $level = 'Level 2';
                    $points = 100;
                } else {
                    $level = 'Level 3';
                    $points = 150;
                }

                \App\Modules\Wallet\Models\QualificationTest::create([
                    'title' => "{$subCat} Audit ({$level})",
                    'description' => "Detailed demographic check targeting consumer behaviors regarding {$subCat} under the {$catName} spectrum.",
                    'reward_points' => $points,
                    'level' => $level,
                    'questions' => [
                        [
                            'text' => "Do you engage in {$subCat} activities or purchasing decisions at least once a month?",
                            'options' => ['Yes', 'No']
                        ],
                        [
                            'text' => "Which option best describes your frequency of {$subCat}?",
                            'options' => ['Daily/Almost Daily', 'Weekly', 'Monthly', 'Rarely/Never']
                        ],
                        [
                            'text' => "Select option A or Yes to pass the attention verification question. (Attention check)",
                            'options' => ['Yes/Option A', 'No/Option B']
                        ]
                    ]
                ]);

                $testId++;
            }
        }
    }

    $output .= "\n[Seeding] Surveys, Academy courses, and 150 Qualification tests seeded successfully!";

    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;'>";
    echo "<h1 style='color: #15803d; margin-top: 0;'>Database Updated Successfully!</h1>";
    echo "<p>Migrations output:</p>";
    echo "<pre style='background: #0f172a; color: #f8fafc; padding: 16px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 13px;'>" . htmlentities($output) . "</pre>";
    echo "<p style='color: #b91c1c; font-weight: bold;'>⚠️ IMPORTANT SECURITY NOTICE: Please delete the <code>update-db.php</code> file from your server immediately after use.</p>";
    echo "</div>";
} catch (\Throwable $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 24px; border: 1px solid #fecaca; border-radius: 8px; background: #fff5f5;'>";
    echo "<h1 style='color: #b91c1c; margin-top: 0;'>Database Migration Failed!</h1>";
    echo "<p>Error details:</p>";
    echo "<pre style='background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 13px;'>" . htmlentities($e->getMessage()) . "</pre>";
    echo "</div>";
}
