<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_plans()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Plans yarat
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'max_projects' => 2,
            'max_team_members' => 3,
            'has_api_access' => false,
            'has_real_time_notifications' => false,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 9.99,
            'max_projects' => -1,
            'max_team_members' => -1,
            'has_api_access' => true,
            'has_real_time_notifications' => true,
            'is_active' => true,
        ]);

        $response = $this->get('/subscriptions/plans');
        $response->assertStatus(200);
        $response->assertSee('Free');
        $response->assertSee('Pro');
    }

    public function test_user_can_subscribe_to_free_plan()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $freePlan = Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'max_projects' => 2,
            'max_team_members' => 3,
            'has_api_access' => false,
            'has_real_time_notifications' => false,
            'is_active' => true,
        ]);

        $response = $this->post("/subscriptions/subscribe/{$freePlan->id}");
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active'
        ]);
    }

    public function test_user_can_subscribe_to_pro_plan()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $proPlan = Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 9.99,
            'max_projects' => -1,
            'max_team_members' => -1,
            'has_api_access' => true,
            'has_real_time_notifications' => true,
            'is_active' => true,
        ]);

        $response = $this->post("/subscriptions/subscribe/{$proPlan->id}");
        $response->assertRedirect("/subscriptions/checkout/{$proPlan->id}");

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $proPlan->id,
            'status' => 'trial'
        ]);
    }

    public function test_user_can_view_subscription_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $plan = Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'max_projects' => 2,
            'max_team_members' => 3,
            'has_api_access' => false,
            'has_real_time_notifications' => false,
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $response = $this->get('/subscriptions/history');
        $response->assertStatus(200);
        $response->assertSee('Free');
    }
}
