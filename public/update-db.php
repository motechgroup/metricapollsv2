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

    $output .= "\n[Seeding] Surveys and Academy courses seeded successfully!";

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
