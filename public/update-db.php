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

    // Clear all configuration, cache, and views programmatically
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    $output .= "\n[Cache-Clear] Config, application cache, routes, and views cleared successfully!";

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

    // Kenya Survey
    $ksurvey = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'Kenya Brand Beverage Audit'],
        [
            'project_id' => $project->id,
            'description' => 'Evaluate brand perception and consumption frequency of Coca-Cola, Pepsi, and local beverage brands across Kenyan households.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 100.00,
            'min_badge_level' => 'Bronze',
            'target_country' => 'Kenya',
        ]
    );
    $ksurvey->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $ksurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Which beverage brand do you purchase most frequently in Kenya?',
        'options' => ['Coca-Cola', 'Pepsi', 'Fanta', 'Club Soda'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $ksurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Confirm you are reading this instruction. (Attention check: Select Coca-Cola)',
        'options' => ['Coca-Cola', 'Pepsi', 'Fanta', 'Club Soda'],
        'sort_order' => 2,
    ]);

    // Tanzania Survey
    $tsurvey = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'Tanzania Dairy Product Survey'],
        [
            'project_id' => $project->id,
            'description' => 'A market study investigating yoghurt and fresh milk brand preferences among consumers in Dar es Salaam, Arusha, and Dodoma.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 100.00,
            'min_badge_level' => 'Bronze',
            'target_country' => 'Tanzania',
        ]
    );
    $tsurvey->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $tsurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Which dairy brand do you buy most often in Tanzania?',
        'options' => ['ASAS', 'Tanga Fresh', 'Shamba', 'Cowbell'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $tsurvey->id,
        'type' => 'single_choice',
        'question_text' => 'To prove attention, choose ASAS. (Attention check: Select ASAS)',
        'options' => ['ASAS', 'Tanga Fresh', 'Shamba', 'Cowbell'],
        'sort_order' => 2,
    ]);

    // Uganda Survey
    $usurvey = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'Uganda Telco Performance Study'],
        [
            'project_id' => $project->id,
            'description' => 'Evaluate cellular connectivity, MTN Mobile Money vs Airtel Money transaction speeds, and mobile data pricing in Kampala and Entebbe.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 100.00,
            'min_badge_level' => 'Bronze',
            'target_country' => 'Uganda',
        ]
    );
    $usurvey->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $usurvey->id,
        'type' => 'single_choice',
        'question_text' => 'What is your primary telecom carrier in Uganda?',
        'options' => ['MTN Uganda', 'Airtel Uganda', 'Lycamobile', 'UTL'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $usurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Choose MTN Uganda to verify attention. (Attention check: Select MTN Uganda)',
        'options' => ['MTN Uganda', 'Airtel Uganda', 'Lycamobile', 'UTL'],
        'sort_order' => 2,
    ]);

    // Rwanda Survey
    $rsurvey = \App\Modules\SurveyEngine\Models\Survey::updateOrCreate(
        ['title' => 'Rwanda Financial Services Inclusivity Audit'],
        [
            'project_id' => $project->id,
            'description' => 'A research study mapping mobile money access, bank agent banking usage, and cash float availability in Kigali and surrounding provinces.',
            'status' => 'published',
            'is_qualification' => false,
            'is_paid' => true,
            'payout_amount' => 100.00,
            'min_badge_level' => 'Bronze',
            'target_country' => 'Rwanda',
        ]
    );
    $rsurvey->questions()->delete();
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $rsurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Which mobile money service do you use most frequently in Rwanda?',
        'options' => ['MTN MoMo Rwanda', 'Airtel Money', 'Bank Account', 'Cash Only'],
        'sort_order' => 1,
    ]);
    \App\Modules\SurveyEngine\Models\Question::create([
        'survey_id' => $rsurvey->id,
        'type' => 'single_choice',
        'question_text' => 'Select MTN MoMo Rwanda to complete this attention check. (Attention check: Select MTN MoMo Rwanda)',
        'options' => ['MTN MoMo Rwanda', 'Airtel Money', 'Bank Account', 'Cash Only'],
        'sort_order' => 2,
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

    \App\Modules\PublicOpinion\Models\AcademyCourse::updateOrCreate(
        ['title' => 'Media & Audience Measurement Methodologies'],
        [
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
        ]
    );

    \App\Modules\PublicOpinion\Models\AcademyCourse::updateOrCreate(
        ['title' => 'Designing Effective Questionnaires & Survey Logic'],
        [
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
        ]
    );

    \App\Modules\PublicOpinion\Models\AcademyCourse::updateOrCreate(
        ['title' => 'Data Validation & Cross-Reference Checks'],
        [
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

    // Populate professional Terms of Service and Privacy Policy matching Metrica Polls' exact nature
    \App\Models\Setting::setValue('terms_of_service', '<h2>1. Acceptance of Terms</h2>
<p>Welcome to Metrica Polls (the "Platform"), owned and operated by Metrica Research. By accessing, registering, or using our public opinion gallery, research portal, panelist dashboard, or academy resources, you agree to be bound by these Terms of Service. If you do not agree to these terms, you must immediately cease using the platform.</p>

<h2>2. Eligibility & Account Security</h2>
<p>To participate as a research panelist, you must be a resident of Kenya, Uganda, Tanzania, or Rwanda, and be at least 18 years of age. You agree to register exclusively using your personal Google Account single sign-on (SSO). Registering or maintaining multiple accounts per individual is strictly forbidden. Any attempt to use duplicate accounts, bots, or automated registration tools will result in permanent account termination and forfeiture of all accumulated rewards.</p>

<h2>3. Paid Surveys, Reward Points, & M-Pesa Payouts</h2>
<p>Panelists earn reward points by participating in eligible survey campaigns. Points are calculated on a <strong>1 Point = 1 KES</strong> basis (or the local currency equivalent for Uganda, Tanzania, and Rwanda). Points can be redeemed for mobile money disbursements (such as Safaricom M-Pesa) once you achieve the minimum redemption threshold configured in your dashboard. You are responsible for ensuring that the phone number linked to your profile is registered to receive mobile money transfer payments.</p>

<h2>4. Anti-Fraud & Response Quality Control Standards</h2>
<p>Metrica Polls enforces strict quality control measures to protect the integrity of the data provided to our corporate clients, NGOs, and government parastatals. Submissions are subject to automatic audit by our security engines, including:
<ul>
    <li><strong>Speed Trap Audit:</strong> Surveys completed significantly faster than normal human reading speeds are automatically flagged.</li>
    <li><strong>Attention Checks:</strong> We embed attention-verification questions. Failing these checks will result in immediate rejection of the survey submission.</li>
    <li><strong>Copy-Paste & Duplication Block:</strong> Open-ended text responses are audited for duplication, plagiarized web snippets, or repetitive copy-paste text.</li>
    <li><strong>Geofencing & Verification:</strong> Panelists must match the geographical targets of the survey. Providing false location profiles is a violation of these terms.</li>
</ul>
Failure to meet these quality standards will result in the rejection of survey responses, forfeiture of points, and eventual suspension of your account.</p>

<h2>5. Intellectual Property & Corporate Briefs</h2>
<p>All survey structures, custom questions, research reports, interactive dashboards, and compiled PDF briefs available in our public opinion gallery or sent to your email are the exclusive intellectual property of Metrica Research and its clients. You may not republish, resell, or distribute these reports for commercial purposes without our express written consent.</p>

<h2>6. Limitation of Liability</h2>
<p>Metrica Research and its third-party service providers shall not be liable for any direct, indirect, incidental, or consequential damages resulting from the use or inability to use the platform, including delayed payout disbursements, system downtimes, or loss of data.</p>');

    \App\Models\Setting::setValue('privacy_policy', '<h2>1. Information We Collect</h2>
<p>We collect personal information directly from you when you register and interact with Metrica Polls. This includes:
<ul>
    <li><strong>Identity Data:</strong> Full name and email address provided during Google single sign-on (SSO) login.</li>
    <li><strong>Demographic Profile Data:</strong> Location, region, phone number, gender, age, household size, occupation, and badges acquired.</li>
    <li><strong>Research Submissions:</strong> Responses, feedback, and text entries submitted during survey campaigns or qualification audits.</li>
</ul>
</p>

<h2>2. How We Use Your Information</h2>
<p>Your personal and demographic data is used to match you with appropriate research campaigns and to analyze public opinions. We aggregate all survey responses to generate research audits and market feasibility reports for our client organizations. <strong>Personally Identifiable Information (PII) is completely encrypted, anonymized, and stripped from all research files</strong> sold to corporate clients, parastatals, or NGOs.</p>

<h2>3. Mobile Money Integrations & Third-Party Sharing</h2>
<p>We share your phone number and full name with mobile money service providers (such as Safaricom M-Pesa) solely to process your requested wallet point redemptions. We do not sell, rent, or distribute your email address, telephone number, or personal details to third-party advertisers or telemarketers.</p>

<h2>4. Anti-Fraud & Audits</h2>
<p>We log IP addresses, submission durations, location metrics, and clipboard behavior during active surveys to prevent fraudulent activities. These logs are processed locally on our secure servers and deleted once response quality verification is complete.</p>

<h2>5. Data Security & Retention</h2>
<p>We employ advanced administrative, physical, and technical controls (including SSL/TLS encryption, database hashing, and role-based access management) to protect your records from loss, alteration, or unauthorized disclosure. We retain your data only for as long as your account remains active or as required by local data protection regulations.</p>

<h2>6. Your Rights & Contact Details</h2>
<p>You have the right to access your stored data, update your details via the profile settings, or request the deletion of your account and all associated personal records at any time. To request account deletion or if you have any privacy questions, please contact our support team at <a href="mailto:support@metricapolls.com">support@metricapolls.com</a>.</p>');

    $output .= "\n[Seeding] Surveys, Academy courses, 150 Qualification tests, and Legal documents seeded/updated successfully!";

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
