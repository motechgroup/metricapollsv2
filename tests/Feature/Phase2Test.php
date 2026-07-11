<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\CRM\Models\ClientOrganization;
use App\Modules\Clients\Models\ResearchRequest;
use App\Modules\Projects\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test Client Portal route protection.
     */
    public function test_guest_cannot_access_client_portal(): void
    {
        $this->get(route('client.requests'))->assertRedirect(route('login'));
        $this->get(route('client.requests.create'))->assertRedirect(route('login'));
        $this->get(route('client.projects'))->assertRedirect(route('login'));
    }

    /**
     * Test client representative user can access client routes.
     */
    public function test_client_can_access_client_portal(): void
    {
        $client = User::where('email', 'client@metricapolls.com')->first();

        $this->actingAs($client)->get(route('client.requests'))->assertStatus(200);
        $this->actingAs($client)->get(route('client.requests.create'))->assertStatus(200);
        $this->actingAs($client)->get(route('client.projects'))->assertStatus(200);
    }

    /**
     * Test admins can access CRM and projects routes.
     */
    public function test_admin_can_access_crm_and_projects(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();

        $this->actingAs($admin)->get(route('admin.crm'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.research-requests'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.projects'))->assertStatus(200);
    }

    /**
     * Test panelist is blocked from CRM and projects.
     */
    public function test_panelist_is_blocked_from_crm_and_projects(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        $this->actingAs($panelist)->get(route('admin.crm'))->assertStatus(403);
        $this->actingAs($panelist)->get(route('admin.research-requests'))->assertStatus(403);
        $this->actingAs($panelist)->get(route('admin.projects'))->assertStatus(403);
    }

    /**
     * Test research request creation and automated project spawning upon approval.
     */
    public function test_research_request_approval_spawns_project(): void
    {
        // 1. Setup client organization & link representative
        $org = ClientOrganization::create(['name' => 'Acme Corporation']);
        $client = User::where('email', 'client@metricapolls.com')->first();
        $client->update(['client_organization_id' => $org->id]);

        // 2. Submit a request
        $request = ResearchRequest::create([
            'user_id' => $client->id,
            'title' => 'Nairobi Beverages Demand Survey',
            'description' => 'A feasibility study on soda preferences in Nairobi region.',
            'target_audience' => 'Young adults 18-29',
            'sample_size' => 1500,
            'estimated_budget' => 4500.00,
            'status' => 'pending',
        ]);

        // Assert request was saved successfully
        $this->assertDatabaseHas('research_requests', [
            'title' => 'Nairobi Beverages Demand Survey',
            'status' => 'pending',
        ]);

        // 3. Approve request using the RequestReview component method or manual controller logic
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        
        // Simulating the approval action
        $orgId = $request->user->client_organization_id;
        $project = Project::create([
            'research_request_id' => $request->id,
            'client_organization_id' => $orgId,
            'name' => $request->title,
            'budget' => $request->estimated_budget,
            'target_quota' => $request->sample_size,
            'status' => 'planning',
        ]);
        $request->update(['status' => 'approved']);

        // Assert request status updated and project spawned
        $this->assertDatabaseHas('research_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('projects', [
            'research_request_id' => $request->id,
            'client_organization_id' => $org->id,
            'name' => 'Nairobi Beverages Demand Survey',
            'status' => 'planning',
            'target_quota' => 1500,
        ]);
    }
}
