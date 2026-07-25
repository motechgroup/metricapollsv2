<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\PublicOpinion\Models\Region;
use App\Modules\PublicOpinion\Models\County;
use App\Modules\PublicOpinion\Models\Constituency;
use App\Modules\PublicOpinion\Models\PoliticalParty;
use App\Modules\PublicOpinion\Models\Politician;
use App\Modules\PublicOpinion\Models\PublicOpinion;
use Livewire\Livewire;
use App\Modules\PublicOpinion\Livewire\AdminPoliticalPartyManager;
use App\Modules\PublicOpinion\Livewire\AdminPoliticianManager;
use App\Modules\PublicOpinion\Livewire\AdminLivePollManager;
use App\Modules\PublicOpinion\Livewire\PublicOpinionPolls;
use App\Modules\PublicOpinion\Livewire\PublicOpinionPollDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PoliticalPollsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'GeographicAndPoliticalSeeder']);
    }

    public function test_geographic_and_political_seeder_populates_data(): void
    {
        $this->assertEquals(8, Region::count());
        $this->assertEquals(47, County::count());
        $this->assertGreaterThan(0, Constituency::count());
        $this->assertGreaterThan(0, PoliticalParty::count());
        $this->assertGreaterThan(0, Politician::count());
        $this->assertGreaterThan(0, PublicOpinion::count());

        $kisiiCounty = County::where('name', 'Kisii')->first();
        $this->assertNotNull($kisiiCounty);
        $this->assertEquals('Nyanza', $kisiiCounty->region->name);
    }

    public function test_admin_can_access_parties_politicians_and_live_polls_management(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Admin can access routes
        $this->actingAs($admin)->get(route('admin.parties.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.politicians.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.live-polls.index'))->assertStatus(200);

        // Panelist forbidden
        $this->actingAs($panelist)->get(route('admin.parties.index'))->assertStatus(403);
    }

    public function test_admin_can_create_political_party_via_livewire(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();

        Livewire::actingAs($admin)
            ->test(AdminPoliticalPartyManager::class)
            ->set('name', 'Nouveau Democratic Alliance')
            ->set('abbreviation', 'NDA')
            ->set('party_color', '#10B981')
            ->set('description', 'Progressive party focus')
            ->call('saveParty')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('political_parties', [
            'name' => 'Nouveau Democratic Alliance',
            'abbreviation' => 'NDA',
            'party_color' => '#10B981',
        ]);
    }

    public function test_admin_can_create_politician_linked_to_county(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $party = PoliticalParty::first();
        $kisii = County::where('name', 'Kisii')->first();

        Livewire::actingAs($admin)
            ->test(AdminPoliticianManager::class)
            ->set('name', 'Hon. Jane Kwamboka')
            ->set('political_party_id', $party->id)
            ->set('level', 'county')
            ->set('region_id', $kisii->region_id)
            ->set('county_id', $kisii->id)
            ->set('position_title', 'Woman Representative')
            ->set('bio', 'Dedicated leader')
            ->call('savePolitician')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('politicians', [
            'name' => 'Hon. Jane Kwamboka',
            'county_id' => $kisii->id,
            'position_title' => 'Woman Representative',
        ]);
    }

    public function test_admin_can_create_live_poll_and_manage_candidate_votes(): void
    {
        $admin = User::where('email', 'admin@metricapolls.com')->first();
        $kisii = County::where('name', 'Kisii')->first();
        $politician = Politician::where('county_id', $kisii->id)->first();

        Livewire::actingAs($admin)
            ->test(AdminLivePollManager::class)
            ->set('topic', 'Kisii County Opinion Audit 2026')
            ->set('target_level', 'county')
            ->set('region_id', $kisii->region_id)
            ->set('county_id', $kisii->id)
            ->set('position_title', 'Woman Representative')
            ->set('status', 'live')
            ->set('allow_public_voting', true)
            ->call('addPoliticianCandidate', $politician->id, 500)
            ->call('createPoll')
            ->assertHasNoErrors();

        $poll = PublicOpinion::where('topic', 'Kisii County Opinion Audit 2026')->first();
        $this->assertNotNull($poll);
        $this->assertEquals(500, $poll->votes_count);
        $this->assertTrue($poll->allow_public_voting);

        // Toggle public voting off
        Livewire::actingAs($admin)
            ->test(AdminLivePollManager::class)
            ->call('togglePublicVoting', $poll->id);

        $this->assertFalse($poll->fresh()->allow_public_voting);
    }

    public function test_visitor_voting_behavior_respects_allow_public_voting_setting(): void
    {
        $response = $this->get(route('public.opinion'));
        $response->assertStatus(200);

        $openPoll = PublicOpinion::where('status', 'live')->where('allow_public_voting', true)->first();
        $disabledPoll = PublicOpinion::where('status', 'live')->where('allow_public_voting', false)->first();

        $this->assertNotNull($openPoll);
        $this->assertNotNull($disabledPoll);

        // Visitor can vote on openPoll
        Livewire::test(PublicOpinionPolls::class)
            ->call('vote', $openPoll->id, $openPoll->options[0])
            ->assertHasNoErrors();

        // Visitor vote rejected on disabledPoll
        Livewire::test(PublicOpinionPolls::class)
            ->call('vote', $disabledPoll->id, $disabledPoll->options[0]);

        $this->assertEquals(0, \App\Modules\PublicOpinion\Models\PublicOpinionVote::where('public_opinion_id', $disabledPoll->id)->count());
    }

    public function test_visitor_can_access_dedicated_poll_page_url(): void
    {
        $poll = PublicOpinion::first();

        $response = $this->get(route('public.opinion.show', $poll->id));
        $response->assertStatus(200);
        $response->assertSee(strtoupper($poll->topic));

        Livewire::test(PublicOpinionPollDetail::class, ['poll' => $poll->id])
            ->assertSet('poll.id', $poll->id);
    }
}
