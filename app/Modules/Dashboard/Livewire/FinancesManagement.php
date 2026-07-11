<?php

namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\GatewaySetting;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\Wallet\Models\Transaction; // For panelist payout stats
use Livewire\Attributes\Title;

#[Title('Company Finances & Payment Gateways - Metrica Polls')]
class FinancesManagement extends Component
{
    // Invoice form fields
    public $invoice_number = '';
    public $project_id = '';
    public $client_organization_id = '';
    public $amount = 0.00;
    public $status = 'pending';

    public $isInvoiceFormOpen = false;

    // Gateway management fields
    public $selectedGatewayId = null;
    public $gatewayCredentials = [];

    protected $rules = [
        'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
        'project_id' => 'required|exists:projects,id',
        'client_organization_id' => 'required|exists:client_organizations,id',
        'amount' => 'required|numeric|min:0',
        'status' => 'required|string|in:pending,paid,cancelled',
    ];

    public function openInvoiceForm()
    {
        $this->resetValidation();
        $this->isInvoiceFormOpen = true;
        
        // Generate automatic invoice number
        $this->invoice_number = 'INV-' . date('Y') . '-' . rand(1000, 9999);
        $this->project_id = '';
        $this->client_organization_id = '';
        $this->amount = 0.00;
        $this->status = 'pending';
    }

    public function closeInvoiceForm()
    {
        $this->isInvoiceFormOpen = false;
    }

    public function saveInvoice()
    {
        $this->validate();

        Invoice::create([
            'invoice_number' => $this->invoice_number,
            'project_id' => $this->project_id,
            'client_organization_id' => $this->client_organization_id,
            'amount' => $this->amount,
            'status' => $this->status,
        ]);

        $this->closeInvoiceForm();
        session()->flash('success', "Invoice '{$this->invoice_number}' created successfully.");
    }

    public function updateInvoiceStatus($invoiceId, $newStatus)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->update(['status' => $newStatus]);
        session()->flash('success', "Invoice '{$invoice->invoice_number}' updated to '{$newStatus}'.");
    }

    public function selectGatewayForEdit($id)
    {
        $gateway = GatewaySetting::findOrFail($id);
        $this->selectedGatewayId = $gateway->id;
        $this->gatewayCredentials = $gateway->credentials;
    }

    public function closeGatewayForm()
    {
        $this->selectedGatewayId = null;
        $this->gatewayCredentials = [];
    }

    public function saveGatewaySettings()
    {
        $gateway = GatewaySetting::findOrFail($this->selectedGatewayId);
        $gateway->update([
            'credentials' => $this->gatewayCredentials,
        ]);

        $this->closeGatewayForm();
        session()->flash('success', "Payment gateway '{$gateway->name}' configurations updated successfully.");
    }

    public function deleteInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        session()->flash('success', 'Invoice deleted successfully.');
    }

    public function render()
    {
        // 1. Calculate statistics
        $invoicesQuery = Invoice::query();
        $totalBilled = Invoice::sum('amount');
        $totalPaid = Invoice::where('status', 'paid')->sum('amount');
        $totalPending = Invoice::where('status', 'pending')->sum('amount');

        // Panelist payouts (simulated using wallet debits/withdrawals)
        $totalPayouts = Transaction::where('type', 'debit')
            ->where('description', 'like', '%withdrawal%')
            ->sum('amount');

        // 2. Fetch data list
        $invoices = Invoice::with(['project', 'clientOrganization'])->orderBy('created_at', 'desc')->get();
        $gateways = GatewaySetting::all();
        $projects = Project::all();
        $organizations = ClientOrganization::all();

        return view('Dashboard::livewire.finances-management', [
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalPayouts' => $totalPayouts,
            'invoices' => $invoices,
            'gateways' => $gateways,
            'projects' => $projects,
            'organizations' => $organizations,
        ])->layout('Dashboard::admin-layout');
    }
}
