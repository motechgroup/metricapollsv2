<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\Projects\Models\Project;
use App\Modules\CRM\Models\ClientOrganization;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Gateways
        DB::table('gateway_settings')->updateOrInsert(
            ['name' => 'M-Pesa'],
            [
                'credentials' => json_encode([
                    'consumer_key' => 'MPESA_DEMO_CONSUMER_KEY',
                    'consumer_secret' => 'MPESA_DEMO_CONSUMER_SECRET',
                    'shortcode' => '174379',
                    'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
                    'environment' => 'sandbox',
                    'status' => 'active',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('gateway_settings')->updateOrInsert(
            ['name' => 'Stripe Card'],
            [
                'credentials' => json_encode([
                    'publishable_key' => 'pk_test_stripe_51P',
                    'secret_key' => 'sk_test_stripe_51P',
                    'environment' => 'sandbox',
                    'status' => 'inactive',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Ensure default organization and project exist
        $org = ClientOrganization::firstOrCreate(['name' => 'Nairobi Transit Agency']);
        
        $project = Project::firstOrCreate(
            ['name' => 'Transit Satisfaction Index'],
            [
                'client_organization_id' => $org->id,
                'status' => 'live',
                'target_quota' => 500,
                'current_responses' => 45,
            ]
        );

        // 3. Seed mock Invoices
        DB::table('invoices')->updateOrInsert(
            ['invoice_number' => 'INV-2026-001'],
            [
                'project_id' => $project->id,
                'client_organization_id' => $org->id,
                'amount' => 4500.00,
                'status' => 'paid',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]
        );

        DB::table('invoices')->updateOrInsert(
            ['invoice_number' => 'INV-2026-002'],
            [
                'project_id' => $project->id,
                'client_organization_id' => $org->id,
                'amount' => 6200.00,
                'status' => 'pending',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]
        );

        DB::table('invoices')->updateOrInsert(
            ['invoice_number' => 'INV-2026-003'],
            [
                'project_id' => $project->id,
                'client_organization_id' => $org->id,
                'amount' => 1250.00,
                'status' => 'cancelled',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ]
        );
    }
}
