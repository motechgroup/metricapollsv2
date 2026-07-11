<?php

namespace App\Modules\PublicOpinion\Livewire;

use Livewire\Component;
use App\Modules\PublicOpinion\Models\MarketplaceReport;
use Livewire\Attributes\Title;

#[Title('Research Report Marketplace - Metrica Polls')]
class Marketplace extends Component
{
    public $purchasedReportId = null;

    public function buy($id)
    {
        $report = MarketplaceReport::findOrFail($id);
        $this->purchasedReportId = $id;

        session()->flash('success', "Purchase complete! Your copy of '{$report->title}' has been unlocked for download.");
    }

    public function download($id)
    {
        $report = MarketplaceReport::findOrFail($id);
        
        // Simulating immediate file download payload
        return response()->streamDownload(function () use ($report) {
            echo "Metrica Polls Research Division\n\nTitle: " . $report->title . "\n\nDescription: " . $report->description . "\n\nThis is a mock commercial research report output generated for demo verification purposes.";
        }, strtolower(str_replace(' ', '_', $report->title)) . '_report.txt');
    }

    public function render()
    {
        // Auto seed marketplace if empty
        if (MarketplaceReport::count() === 0) {
            MarketplaceReport::create([
                'title' => 'East Africa Consumer Outlook 2026',
                'description' => 'A comprehensive review of retail behaviors, pricing indexes, and purchasing trends across Kenya, Tanzania, and Uganda.',
                'price' => 49.00,
            ]);

            MarketplaceReport::create([
                'title' => 'Kenya Fintech & Mobile Money Adoption Audit',
                'description' => 'Analyzing M-Pesa merchant growth, consumer credit profiles, and cross-border digital payment conversions.',
                'price' => 29.00,
            ]);
        }

        $reports = MarketplaceReport::all();

        return view('PublicOpinion::livewire.marketplace', [
            'reports' => $reports,
        ])->layout('Corporate::layout');
    }
}
