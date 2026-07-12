<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\AdminPoll;
use App\Modules\PublicOpinion\Models\ReportDownload;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Public Reports Gallery - Metrica Polls')]
class PublicReportsGallery extends Component
{
    public $selectedReportId = null;
    public $downloadingReportId = null;
    public $visitorEmail = '';

    protected $rules = [
        'visitorEmail' => 'required|email|max:255',
    ];

    public function selectReport($id)
    {
        $this->selectedReportId = $id;
        $this->downloadingReportId = null;
        $this->visitorEmail = '';
    }

    public function closeReport()
    {
        $this->selectedReportId = null;
        $this->downloadingReportId = null;
        $this->visitorEmail = '';
    }

    public function downloadPrompt($id)
    {
        $this->downloadingReportId = $id;
        $this->visitorEmail = '';
    }

    public function cancelDownload()
    {
        $this->downloadingReportId = null;
        $this->visitorEmail = '';
    }

    public function submitDownload()
    {
        $this->validate();

        $poll = AdminPoll::findOrFail($this->downloadingReportId);

        // Record lead capture download
        ReportDownload::create([
            'admin_poll_id' => $poll->id,
            'email' => $this->visitorEmail,
        ]);

        // Increment download counter
        $poll->increment('download_count');

        $emailCopy = $this->visitorEmail;
        $this->downloadingReportId = null;
        $this->visitorEmail = '';

        session()->flash('success', "Report generated and sent to {$emailCopy} successfully!");

        // 1. Process base64 branded logo for PDF compatibility
        $logoBase64 = null;
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        // 2. Generate PDF using Barryvdh\DomPDF\Facade\Pdf
        $pdf = Pdf::loadView('PublicOpinion::pdf.report', [
            'poll' => $poll,
            'email' => $emailCopy,
            'logoBase64' => $logoBase64,
        ]);

        $pdfContent = $pdf->output();
        $filename = strtolower(str_replace(' ', '_', $poll->title)) . '_report.pdf';

        // 3. Dispatch PDF report email natively
        try {
            $subject = "Your Metrica Polls Public Opinion Report";
            $body = "Dear Reader,\n\nPlease find attached the public opinion report you requested from Metrica Polls:\n\n**Report:** {report_title}\n**Category:** {category}\n**Release Date:** {release_date}\n\nThank you for choosing Metrica Polls for market intelligence.";
            
            \Illuminate\Support\Facades\Mail::to($emailCopy)->send(new \App\Mail\CustomConfigurableMail(
                $subject,
                $body,
                [
                    'report_title' => $poll->title,
                    'category' => $poll->category,
                    'release_date' => $poll->release_date,
                ],
                $pdfContent,
                $filename,
                'application/pdf'
            ));
            
            logger("Successfully emailed PDF report to {$emailCopy}");
        } catch (\Throwable $e) {
            logger("Failed to dispatch PDF report email to {$emailCopy}: " . $e->getMessage());
        }

        // 4. Return stream download of PDF content
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function seedReports()
    {
        // 1. Government & NGO Net study
        if (!AdminPoll::where('title', 'March 2025 Uganda Malaria Prevention Net Distribution Survey')->exists()) {
            AdminPoll::create([
                'title' => 'March 2025 Uganda Malaria Prevention Net Distribution Survey',
                'category' => 'Product Feasibility',
                'is_public' => true,
                'research_date' => '2025-03-05',
                'release_date' => '2025-03-25',
                'initial_downloads' => 410,
                'download_count' => 0,
                'sample_size' => 1500,
                'region' => 'Eastern & Northern Uganda',
                'methodology' => 'Digitized mobile surveys and randomly selected community focus groups funded by health NGOs and the Ministry of Health.',
                'options' => [
                    ['name' => 'Gov Distributed Nets', 'votes' => 920, 'logo' => '/favicon.png'],
                    ['name' => 'Private Purchase Nets', 'votes' => 380, 'logo' => '/favicon.png'],
                    ['name' => 'No Net Usage', 'votes' => 200, 'logo' => '/favicon.png'],
                ],
                'ai_report' => "### METRICA RESEARCH INSIGHTS & STRATEGIC OUTLOOK\n\n" .
                    "**1. STUDY PARAMETERS**\n" .
                    "- Focus: March 2025 Uganda Malaria Prevention Net Distribution\n" .
                    "- Client Base: Ministry of Health, WHO, and partnering Healthcare NGOs\n" .
                    "- Sample Size: 1,500 households\n\n" .
                    "**2. DATA SUMMARY**\n" .
                    "Government Distributed Nets command a major share of **61.3%** of households. Private purchase nets account for 25.3%, and 13.3% report no net usage.\n\n" .
                    "**3. RESEARCH FINDINGS**\n" .
                    "- Nets distributed via public health channels represent the primary barrier against vector-borne transmissions.\n" .
                    "- Vulnerable rural cohorts exhibit the highest utilization rates, while urban areas buy nets commercially.\n\n" .
                    "**4. STRATEGIC RECOMMENDATIONS**\n" .
                    "1. Scale public health net drops in remote northern regions.\n" .
                    "2. Address net retreatment education to ensure long-term protective efficiency.",
            ]);
        }

        // 2. Corporate EABL study
        if (!AdminPoll::where('title', 'March 2025 East Africa Beverage Market Penetration Audit')->exists()) {
            AdminPoll::create([
                'title' => 'March 2025 East Africa Beverage Market Penetration Audit',
                'category' => 'Brand Performance',
                'is_public' => true,
                'research_date' => '2025-03-10',
                'release_date' => '2025-03-28',
                'initial_downloads' => 580,
                'download_count' => 0,
                'sample_size' => 2200,
                'region' => 'Kenya & Tanzania',
                'methodology' => 'Structured SMS questionnaires and retail inventory tracking across major retail chains.',
                'options' => [
                    ['name' => 'Tusker Lager', 'votes' => 1250, 'logo' => '/favicon.png'],
                    ['name' => 'Safari Lager', 'votes' => 680, 'logo' => '/favicon.png'],
                    ['name' => 'Senene Brew', 'votes' => 270, 'logo' => '/favicon.png'],
                ],
                'ai_report' => "### METRICA RESEARCH INSIGHTS & STRATEGIC OUTLOOK\n\n" .
                    "**1. STUDY PARAMETERS**\n" .
                    "- Focus: March 2025 Beverage Preference and Volume Audit\n" .
                    "- Client Base: East Africa Breweries (EABL) & Corporate Retail Distributors\n" .
                    "- Sample Size: 2,200 retail consumers\n\n" .
                    "**2. DATA SUMMARY**\n" .
                    "Tusker Lager leads with **56.8%** market share. Safari Lager follows at 30.9%, and Senene Brew holds 12.3%.\n\n" .
                    "**3. RESEARCH FINDINGS**\n" .
                    "- Tusker remains the dominant brand in metropolitan areas due to long-standing brand equity.\n" .
                    "- Safari Lager is closing the gap in transit-hub markets.\n\n" .
                    "**4. STRATEGIC RECOMMENDATIONS**\n" .
                    "1. Optimize local supply lines to prevent weekend stockouts in expanding retail channels.\n" .
                    "2. Introduce targeted marketing for mid-tier brands in rural locations.",
            ]);
        }

        // 3. Parastatal BRT Transit study
        if (!AdminPoll::where('title', 'March 2025 Nairobi BRT Line 1 Transit Preference Survey')->exists()) {
            AdminPoll::create([
                'title' => 'March 2025 Nairobi BRT Line 1 Transit Preference Survey',
                'category' => 'Product Feasibility',
                'is_public' => true,
                'research_date' => '2025-03-02',
                'release_date' => '2025-03-20',
                'initial_downloads' => 670,
                'download_count' => 0,
                'sample_size' => 3000,
                'region' => 'Nairobi Metro, Kenya',
                'methodology' => 'Random digit dialing and station-intercept questionnaires conducted by parastatal transit auditors.',
                'options' => [
                    ['name' => 'BRT Commute', 'votes' => 1950, 'logo' => '/favicon.png'],
                    ['name' => 'Matatu Service', 'votes' => 850, 'logo' => '/favicon.png'],
                    ['name' => 'Personal Vehicle', 'votes' => 200, 'logo' => '/favicon.png'],
                ],
                'ai_report' => "### METRICA RESEARCH INSIGHTS & STRATEGIC OUTLOOK\n\n" .
                    "**1. STUDY PARAMETERS**\n" .
                    "- Focus: Nairobi Bus Rapid Transit (BRT) Line 1 Adoption Feasibility\n" .
                    "- Client Base: Nairobi Metropolitan Area Transit Authority (NAMATA) & Parastatals\n" .
                    "- Sample Size: 3,000 commuters\n\n" .
                    "**2. DATA SUMMARY**\n" .
                    "BRT Commute exhibits high demand at **65.0%** interest. Matatu services account for 28.3%, and personal vehicles hold 6.7%.\n\n" .
                    "**3. RESEARCH FINDINGS**\n" .
                    "- Commuters prioritize travel time reliability over cost, driving high interest in dedicated BRT lanes.\n" .
                    "- Integrated ticketing systems are identified as a critical success factor for adoption.\n\n" .
                    "**4. STRATEGIC RECOMMENDATIONS**\n" .
                    "1. Execute public awareness campaigns demonstrating BRT speed benefits.\n" .
                    "2. Implement cashless payment platforms to minimize boarding delays.",
            ]);
        }

        // 4. Parastatal & Media Reach study
        if (!AdminPoll::where('title', 'March 2025 East Africa Radio Audience Share Measurement')->exists()) {
            AdminPoll::create([
                'title' => 'March 2025 East Africa Radio Audience Share Measurement',
                'category' => 'Media Audience Measurement',
                'is_public' => true,
                'research_date' => '2025-03-12',
                'release_date' => '2025-03-30',
                'initial_downloads' => 890,
                'download_count' => 0,
                'sample_size' => 4500,
                'region' => 'Tanzania & Rwanda',
                'methodology' => 'Day-after-recall telephone interviews and diary logs conducted across rural and urban radio cohorts.',
                'options' => [
                    ['name' => 'Radio Free Africa', 'votes' => 2100, 'logo' => '/favicon.png'],
                    ['name' => 'Clouds FM', 'votes' => 1600, 'logo' => '/favicon.png'],
                    ['name' => 'Radio Rwanda', 'votes' => 800, 'logo' => '/favicon.png'],
                ],
                'ai_report' => "### METRICA RESEARCH INSIGHTS & STRATEGIC OUTLOOK\n\n" .
                    "**1. STUDY PARAMETERS**\n" .
                    "- Focus: East Africa Radio Audience Share Measurement\n" .
                    "- Client Base: Media Houses, Ad Agencies, and National Broadcasting Regulators\n" .
                    "- Sample Size: 4,500 listeners\n\n" .
                    "**2. DATA SUMMARY**\n" .
                    "Radio Free Africa commands **46.7%** share-of-ear. Clouds FM holds 35.6%, and Radio Rwanda accounts for 17.8%.\n\n" .
                    "**3. RESEARCH FINDINGS**\n" .
                    "- Radio Free Africa maintains a dominant lead in sub-urban agricultural segments.\n" .
                    "- Clouds FM is favored by youth demographics, supporting higher advertising premium rates during afternoon blocks.\n\n" .
                    "**4. STRATEGIC RECOMMENDATIONS**\n" .
                    "1. Align advertising rates dynamically with audience age distributions.\n" .
                    "2. Expand digital web-streaming to capture transit and diaspora listeners.",
            ]);
        }
    }

    public function render()
    {
        // Force seed all 4 detailed reports if missing
        $this->seedReports();

        $polls = AdminPoll::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeReport = $this->selectedReportId ? AdminPoll::find($this->selectedReportId) : null;

        return view('PublicOpinion::livewire.public-reports-gallery', [
            'polls' => $polls,
            'activeReport' => $activeReport,
        ])->layout('Corporate::layout');
    }
}
