<?php

namespace App\Modules\Corporate\Controllers;

use App\Http\Controllers\Controller;

class CorporateController extends Controller
{
    public function index()
    {
        // Prevent database exceptions before surveys table is migrated
        if (!\Illuminate\Support\Facades\Schema::hasTable('surveys')) {
            return view('Corporate::index', ['surveys' => []]);
        }

        // Auto-seed default paid & qualification surveys if none exist
        if (\App\Modules\SurveyEngine\Models\Survey::where('is_paid', true)->orWhere('is_qualification', true)->count() === 0) {
            $org = \App\Modules\CRM\Models\ClientOrganization::firstOrCreate(
                ['name' => 'Default Organization']
            );

            $project = \App\Modules\Projects\Models\Project::firstOrCreate(
                ['name' => 'General Opinion Index'],
                [
                    'client_organization_id' => $org->id,
                    'status' => 'live',
                    'target_quota' => 1000,
                    'current_responses' => 12,
                ]
            );

            // 1. Qualification test
            $qSurvey = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'Metrica Analyst Entry Test & Training Survey',
                'description' => 'A mandatory qualification test covering basic survey consistency, regional familiarity, and reading checks to verify panelist credentials.',
                'status' => 'published',
                'is_qualification' => true,
                'is_paid' => false,
                'payout_amount' => 0.00,
                'min_badge_level' => 'Bronze',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey->id,
                'type' => 'single_choice',
                'question_text' => 'Which city is the capital of Kenya? (Attention check: Select Nairobi)',
                'options' => ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey->id,
                'type' => 'text',
                'question_text' => 'Tell us briefly why you want to become a Metrica panelist.',
                'sort_order' => 2,
            ]);

            // 2. High paying survey
            $pSurvey = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'East Africa Digital Payments Survey 2026',
                'description' => 'A corporate research study investigating consumer M-Pesa, Airtel Money, and bank transfer usage frequencies across major urban centers.',
                'status' => 'published',
                'is_qualification' => false,
                'is_paid' => true,
                'payout_amount' => 350.00,
                'min_badge_level' => 'Silver',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey->id,
                'type' => 'single_choice',
                'question_text' => 'What is your primary mobile wallet service provider?',
                'options' => ['Safaricom M-Pesa', 'Airtel Money', 'T-Kash', 'Bank App'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey->id,
                'type' => 'single_choice',
                'question_text' => 'Confirm you are reading this question. (Attention check: Select Safaricom M-Pesa)',
                'options' => ['Safaricom M-Pesa', 'Airtel Money', 'T-Kash', 'Bank App'],
                'sort_order' => 2,
            ]);

            // 3. Data Quality & Anti-Fraud Compliance Training
            $qSurvey2 = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'Survey Data Quality & Anti-Fraud Compliance Training',
                'description' => 'Learn the fundamentals of consistent data submission. This training teaches panelists how to read questions diligently, avoid rushing, and answer attention checks correctly.',
                'status' => 'published',
                'is_qualification' => true,
                'is_paid' => false,
                'payout_amount' => 0.00,
                'min_badge_level' => 'Bronze',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey2->id,
                'type' => 'single_choice',
                'question_text' => 'To avoid timing traps, what is the minimum duration you should spend reading and answering each question? (Attention check: Select 5 seconds)',
                'options' => ['Instant submission', '2 seconds', '5 seconds', 'No limit'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey2->id,
                'type' => 'single_choice',
                'question_text' => 'Select Option C to prove you are paying attention to this instruction. (Attention check: Select Option C)',
                'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                'sort_order' => 2,
            ]);

            // 4. Field Research & Interview Ethics Training
            $qSurvey3 = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'Field Research Methodologies & Respondent Ethics Guide',
                'description' => 'Mandatory training module for field operations, mapping, and interview methodologies across East Africa.',
                'status' => 'published',
                'is_qualification' => true,
                'is_paid' => false,
                'payout_amount' => 0.00,
                'min_badge_level' => 'Bronze',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey3->id,
                'type' => 'single_choice',
                'question_text' => 'What is the primary rule when obtaining consumer consent for research audits? (Attention check: Select Express Consent)',
                'options' => ['Implicit Consent', 'Express Consent', 'Skip Consent', 'No Rule'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $qSurvey3->id,
                'type' => 'single_choice',
                'question_text' => 'What coordinates are logged to guarantee GPS audit trail integrity? (Attention check: Select Local Device Coordinates)',
                'options' => ['Mock Coordinates', 'Any Coordinates', 'Local Device Coordinates', 'None'],
                'sort_order' => 2,
            ]);

            // 5. Consumer Retail & FMCG Buying Habits 2026
            $pSurvey2 = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'Consumer Retail & FMCG Buying Habits 2026',
                'description' => 'An FMCG research campaign auditing supermarket product selections, laundry detergent brands, and cooking oil brand loyalties across households.',
                'status' => 'published',
                'is_qualification' => false,
                'is_paid' => true,
                'payout_amount' => 450.00,
                'min_badge_level' => 'Silver',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey2->id,
                'type' => 'single_choice',
                'question_text' => 'How often do you shop for household groceries?',
                'options' => ['Daily', 'Weekly', 'Monthly', 'Rarely'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey2->id,
                'type' => 'single_choice',
                'question_text' => 'Confirm you are human. (Attention check: Select Weekly)',
                'options' => ['Daily', 'Weekly', 'Monthly', 'Rarely'],
                'sort_order' => 2,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey2->id,
                'type' => 'single_choice',
                'question_text' => 'Which factor is most important when choosing a cooking oil brand?',
                'options' => ['Price', 'Quality', 'Brand Trust', 'Health benefits'],
                'sort_order' => 3,
            ]);

            // 6. East African Telco & Internet Connectivity Census
            $pSurvey3 = \App\Modules\SurveyEngine\Models\Survey::create([
                'project_id' => $project->id,
                'title' => 'East African Telco & Internet Connectivity Census',
                'description' => 'A comprehensive study on cellular data packages, fibre internet reliability, and customer service satisfaction ratings.',
                'status' => 'published',
                'is_qualification' => false,
                'is_paid' => true,
                'payout_amount' => 600.00,
                'min_badge_level' => 'Gold',
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey3->id,
                'type' => 'single_choice',
                'question_text' => 'What is your primary home internet connection method?',
                'options' => ['Mobile Data hotspot', 'Fibre-to-the-Home', 'Fixed LTE', 'No Internet'],
                'sort_order' => 1,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey3->id,
                'type' => 'single_choice',
                'question_text' => 'To prove attention, choose Fibre-to-the-Home. (Attention check: Select Fibre-to-the-Home)',
                'options' => ['Mobile Data hotspot', 'Fibre-to-the-Home', 'Fixed LTE', 'No Internet'],
                'sort_order' => 2,
            ]);

            \App\Modules\SurveyEngine\Models\Question::create([
                'survey_id' => $pSurvey3->id,
                'type' => 'single_choice',
                'question_text' => 'How would you rate your telco\'s customer care responsiveness?',
                'options' => ['Excellent', 'Good', 'Average', 'Poor'],
                'sort_order' => 3,
            ]);
        }

        $surveys = \App\Modules\SurveyEngine\Models\Survey::where('status', 'published')
            ->where(function ($query) {
                $query->where('is_paid', true)
                    ->orWhere('is_qualification', true);
            })
            ->get();

        return view('Corporate::index', compact('surveys'));
    }

    public function features()
    {
        return view('Corporate::features');
    }

    public function pricing()
    {
        return view('Corporate::pricing');
    }

    public function about()
    {
        return view('Corporate::about');
    }

    public function contact()
    {
        return view('Corporate::contact');
    }

    public function maintenance()
    {
        return view('Corporate::maintenance');
    }

    public function terms()
    {
        $content = \App\Models\Setting::getValue('terms_of_service', '');
        return view('Corporate::terms', compact('content'));
    }

    public function privacy()
    {
        $content = \App\Models\Setting::getValue('privacy_policy', '');
        return view('Corporate::privacy', compact('content'));
    }
}
