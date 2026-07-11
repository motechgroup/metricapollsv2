<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Wallet\Models\PanelistProfile;
use App\Modules\Wallet\Models\QualificationTest;
use App\Modules\Wallet\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase4Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    }

    /**
     * Test panelist profile demographics and verification bonus points.
     */
    public function test_panelist_profile_completion_awards_bonus(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Create initial profile
        $profile = PanelistProfile::create([
            'user_id' => $panelist->id,
            'points_balance' => 0,
            'is_verified' => false,
        ]);

        // Simulate profile update and verification
        $profile->update([
            'gender' => 'Female',
            'date_of_birth' => '1995-08-12',
            'education_level' => 'Bachelors Degree',
            'income_bracket' => '$1,500 - $3,500',
            'location_region' => 'Nairobi East',
            'is_verified' => true,
            'points_balance' => 100, // Profile completion award
        ]);

        Transaction::create([
            'user_id' => $panelist->id,
            'type' => 'reward',
            'amount' => 1.00,
            'points' => 100,
            'description' => 'Profiling verification bonus',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('panelists', [
            'user_id' => $panelist->id,
            'is_verified' => true,
            'points_balance' => 100,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $panelist->id,
            'type' => 'reward',
            'points' => 100,
        ]);
    }

    /**
     * Test wallet points redemption payout simulation.
     */
    public function test_wallet_points_redemption_deducts_balance(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        // Setup verified profile with 600 points
        $profile = PanelistProfile::create([
            'user_id' => $panelist->id,
            'gender' => 'Male',
            'is_verified' => true,
            'points_balance' => 600,
        ]);

        // Redeem 500 points
        $pointsToRedeem = 500;
        $profile->decrement('points_balance', $pointsToRedeem);

        Transaction::create([
            'user_id' => $panelist->id,
            'type' => 'withdrawal',
            'amount' => -5.00,
            'points' => -$pointsToRedeem,
            'description' => 'Redeemed points to mobile money',
            'reference' => 'MPESA-TESTREF',
            'status' => 'completed',
        ]);

        $this->assertEquals(100, $profile->fresh()->points_balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $panelist->id,
            'type' => 'withdrawal',
            'points' => -500,
            'amount' => -5.00,
            'reference' => 'MPESA-TESTREF',
        ]);
    }

    /**
     * Test qualification test completion and rewards.
     */
    public function test_qualification_test_completion_awards_points(): void
    {
        $panelist = User::where('email', 'panelist@metricapolls.com')->first();

        $profile = PanelistProfile::create([
            'user_id' => $panelist->id,
            'points_balance' => 0,
            'is_verified' => false,
        ]);

        $test = QualificationTest::create([
            'title' => 'Consumer Purchasing Habits Qualification',
            'reward_points' => 50,
            'questions' => [
                ['text' => 'Do you shop online?', 'options' => ['Yes', 'No']]
            ]
        ]);

        // Complete qualification
        $profile->increment('points_balance', $test->reward_points);
        
        DB::table('panelist_qualifications')->insert([
            'user_id' => $panelist->id,
            'qualification_test_id' => $test->id,
            'passed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Transaction::create([
            'user_id' => $panelist->id,
            'type' => 'reward',
            'amount' => 0.50,
            'points' => $test->reward_points,
            'description' => 'Completed qualification: ' . $test->title,
            'status' => 'completed',
        ]);

        $this->assertEquals(50, $profile->fresh()->points_balance);

        $this->assertDatabaseHas('panelist_qualifications', [
            'user_id' => $panelist->id,
            'qualification_test_id' => $test->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $panelist->id,
            'type' => 'reward',
            'points' => 50,
        ]);
    }
}
