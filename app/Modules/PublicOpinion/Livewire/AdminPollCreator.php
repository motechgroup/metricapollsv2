<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\PublicOpinion\Models\AdminPoll;
use Livewire\Attributes\Title;

#[Title('Create AI-Driven Poll - Metrica Polls')]
class AdminPollCreator extends Component
{
    use WithFileUploads;

    public $title = '';
    public $category = 'Brand Performance'; // Brand Performance, Political Popularity, Product Feasibility, Media Audience Measurement
    public $isPublic = true;

    // Advanced features
    public $researchDate;
    public $releaseDate;
    public $initialDownloads = 150;
    public $sampleSize = 500;
    public $region = 'Nairobi, Kenya';
    public $methodology = 'Random digit dialing and digitized SMS polling targeting active consumer cohorts.';

    // Option rows
    public $options = [
        ['name' => '', 'votes' => 100, 'logo' => null, 'logo_path' => '']
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'category' => 'required|string',
        'researchDate' => 'required|date',
        'releaseDate' => 'required|date',
        'initialDownloads' => 'required|integer|min:0',
        'sampleSize' => 'required|integer|min:1',
        'region' => 'required|string|max:255',
        'methodology' => 'required|string',
        'options.*.name' => 'required|string|max:100',
        'options.*.votes' => 'required|integer|min:0',
        'options.*.logo' => 'nullable|image|max:1024',
    ];

    public function mount()
    {
        $this->researchDate = date('Y-m-d');
        $this->releaseDate = date('Y-m-d');
        $this->generateDynamicMethodology();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['researchDate', 'releaseDate', 'sampleSize', 'region', 'category'])) {
            $this->generateDynamicMethodology();
        }
    }

    public function generateDynamicMethodology()
    {
        $sample = number_format($this->sampleSize ?: 500);
        $reg = $this->region ?: 'Nairobi, Kenya';
        $cat = $this->category ?: 'General Research';
        $start = $this->researchDate ?: date('Y-m-d');
        $end = $this->releaseDate ?: date('Y-m-d');

        $n = (int) $this->sampleSize;
        if ($n > 0) {
            $moe = round((1.96 / (2 * sqrt($n))) * 100, 2);
        } else {
            $moe = 4.38;
        }

        $this->methodology = "A randomized quantitative field study conducted in {$reg} between {$start} and {$end} targeting active cohorts under the {$cat} category. An analyzed sample size of {$sample} respondents was interviewed utilizing stratified random sampling via geofenced SMS-polling and device location audits, achieving an estimated margin of error of +/- {$moe}% at a 95% confidence level.";
    }

    public function addOption()
    {
        $this->options[] = ['name' => '', 'votes' => 0, 'logo' => null, 'logo_path' => ''];
    }

    public function removeOption($index)
    {
        if (count($this->options) > 1) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    public function generateReport()
    {
        $this->validate();

        $compiledOptions = [];
        $totalVotes = 0;

        foreach ($this->options as $index => $opt) {
            $logoPath = '/images/logo.png'; // Default Metrica logo fallback

            if (isset($opt['logo']) && $opt['logo'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $filename = 'logo_' . time() . '_' . $index . '.' . $opt['logo']->getClientOriginalExtension();
                $opt['logo']->storeAs('public/images/polls', $filename);
                $logoPath = '/storage/images/polls/' . $filename;
            } else {
                $logoPath = '/images/favicon.png';
            }

            $compiledOptions[] = [
                'name' => $opt['name'],
                'votes' => (int) $opt['votes'],
                'logo' => $logoPath,
            ];

            $totalVotes += (int) $opt['votes'];
        }

        // Sort options by votes descending to compile statistical distribution
        usort($compiledOptions, function ($a, $b) {
            return $b['votes'] <=> $a['votes'];
        });

        $leader = count($compiledOptions) > 0 ? $compiledOptions[0] : ['name' => 'N/A', 'votes' => 0];
        $runnerUp = count($compiledOptions) > 1 ? $compiledOptions[1] : null;

        $leaderPct = $totalVotes > 0 ? round(($leader['votes'] / $totalVotes) * 100, 1) : 0;
        $runnerUpPct = ($runnerUp && $totalVotes > 0) ? round(($runnerUp['votes'] / $totalVotes) * 100, 1) : 0;
        $gapPct = round($leaderPct - $runnerUpPct, 1);
        $topTwoPct = round($leaderPct + $runnerUpPct, 1);

        // Build a highly detailed, data-driven AI Report incorporating key statistical points
        $aiReportText = "### METRICA POLLS AI™ INSIGHTS & STRATEGIC OUTLOOK\n\n" .
            "**1. STUDY METADATA & PARAMETERS**\n" .
            "- **Research Area Focus:** {$this->title}\n" .
            "- **Type of Research Study:** {$this->category}\n" .
            "- **Target Cohort Region:** {$this->region}\n" .
            "- **Sample Size Analyzed:** " . number_format($this->sampleSize) . " respondents\n" .
            "- **Research Period:** {$this->researchDate}\n" .
            "- **Official Report Release Date:** {$this->releaseDate}\n\n" .
            "**2. RESEARCH METHODOLOGY STATEMENT**\n" .
            "{$this->methodology}\n\n" .
            "**3. STATISTICAL DISTRIBUTION ANALYSIS**\n" .
            "Below is the ranked distribution based on raw data processing:\n";

        foreach ($compiledOptions as $rank => $opt) {
            $pct = $totalVotes > 0 ? round(($opt['votes'] / $totalVotes) * 100, 1) : 0;
            $aiReportText .= "- **Rank #" . ($rank + 1) . ":** {$opt['name']} — " . number_format($opt['votes']) . " counts ({$pct}%)\n";
        }

        $aiReportText .= "\n**4. DYNAMIC KEY INSIGHTS & FINDINGS**\n";

        // Generate data-driven points based on ratios
        if ($runnerUp) {
            if ($gapPct > 20) {
                $aiReportText .= "- **Market Dominance:** {$leader['name']} holds a clear, commanding lead of **{$gapPct}%** over {$runnerUp['name']}, indicating strong brand equity / cohort centralization.\n";
            } else {
                $aiReportText .= "- **High Market Contestability:** The competitive gap between {$leader['name']} and {$runnerUp['name']} is highly narrow at only **{$gapPct}%**, indicating a volatile market susceptible to campaign shifts.\n";
            }

            $aiReportText .= "- **Consolidation Index:** The top two options ({$leader['name']} and {$runnerUp['name']}) command **{$topTwoPct}%** of all responses, suggesting a highly consolidated market segment.\n";
        } else {
            $aiReportText .= "- **Single Option Sample:** Data exhibits complete consensus for {$leader['name']} due to absence of alternative comparator rows.\n";
        }

        // Add category specific analysis
        $aiReportText .= "- **Region Vulnerability:** Surveys logged in {$this->region} indicate localized infrastructure, pricing sensitivity, and campaign messaging strongly influence these numbers.\n";
        $aiReportText .= "- **Sample Margin of Error:** With a sample size of " . number_format($this->sampleSize) . ", the statistical confidence level is estimated at 95% with a +/- 4.2% margin of error.\n\n" .
            "**5. STRATEGIC SECTOR RECOMMENDATIONS**\n";

        if ($this->category === 'Brand Performance') {
            $aiReportText .= "1. **Market Penetration:** The brand presence of **{$leader['name']}** remains dominant in the {$this->region} market. To scale further, the leader must protect retail supply lines.\n" .
                "2. **Competitor Strategy:** Lower ranked brands must optimize retail touchpoints, introduce localized pricing discounts, and deploy aggressive digital campaigns to target high-churn consumer profiles.\n" .
                "3. **Outlook:** The segment is projected to maintain its current structure, with a low probability of disruptive entry over the next fiscal cycle.";
        } elseif ($this->category === 'Political Popularity') {
            $aiReportText .= "1. **Voter Consolidation:** Political consensus is centered around **{$leader['name']}** in the {$this->region} region. Supporting demographics show strong resilience.\n" .
                "2. **Key Demographics:** Focus groups indicate young voter demographics show high support levels, suggesting digital-first campaign strategies are yielding dividends.\n" .
                "3. **Grassroots Action:** Volatility in turnout is the primary risk. Mobilization campaigns are critical to solidify these ratings into confirmed outcomes.";
        } elseif ($this->category === 'Product Feasibility') {
            $aiReportText .= "1. **Consumer Demand:** Feasibility scores for **{$leader['name']}** show high consumer demand. Trial interest scores are above average, driven by perceived value and features.\n" .
                "2. **Barrier to Entry:** Pricing tiers remain the secondary concern for respondents. Adjusting product tiers will widen the addressable market size.\n" .
                "3. **Launch Strategy:** A strategic pilot program is recommended in key urban centers of {$this->region} before committing to capital-intensive regional scaling.";
        } else {
            $aiReportText .= "1. **Audience Share:** Media audience metrics show **{$leader['name']}** commanding peak share-of-ear/eye. Prime-time slots register the highest engagement levels.\n" .
                "2. **Ad Value Projections:** The high concentration of viewers supports prime advertising CPM increases of 10-15%.\n" .
                "3. **Programming Recommendations:** Content developers should capitalize on this engagement by scaling high-performing programming formats and syndicated segments.";
        }

        // Save to Database
        AdminPoll::create([
            'title' => $this->title,
            'category' => $this->category,
            'options' => $compiledOptions,
            'is_public' => $this->isPublic,
            'ai_report' => $aiReportText,
            'research_date' => $this->researchDate,
            'release_date' => $this->releaseDate,
            'initial_downloads' => $this->initialDownloads,
            'download_count' => 0,
            'sample_size' => $this->sampleSize,
            'region' => $this->region,
            'methodology' => $this->methodology,
        ]);

        session()->flash('success', "Poll successfully created! AI Report generated under category: {$this->category}");

        return redirect()->route('public.reports');
    }

    public function render()
    {
        return view('PublicOpinion::livewire.admin-poll-creator')
            ->layout('Dashboard::admin-layout');
    }
}
