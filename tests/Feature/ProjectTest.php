<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Free plan yarat
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

        // User-ə free subscription ver
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $response = $this->post('/projects', [
            'name' => 'Test Project',
            'description' => 'Test Description'
        ]);

        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);
    }

    public function test_user_cannot_exceed_project_limit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Free plan yarat (max 2 projects)
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

        // User-ə free subscription ver
        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        // 2 layihə yarat
        Project::create([
            'name' => 'Project 1',
            'description' => 'Description 1',
            'user_id' => $user->id
        ]);

        Project::create([
            'name' => 'Project 2',
            'description' => 'Description 2',
            'user_id' => $user->id
        ]);

        // 3-cü layihə yaratmağa çalış
        $response = $this->get('/projects/create');
        $response->assertRedirect('/subscriptions/plans');
    }

    public function test_user_can_view_own_projects()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);

        // User-ı project-ə member kimi əlavə et
        $project->members()->attach($user->id, ['role' => 'owner']);

        $response = $this->get('/projects');
        $response->assertStatus(200);
        $response->assertSee('Test Project');
    }

    public function test_user_cannot_view_other_users_projects()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user1);

        $project = Project::create([
            'name' => 'Other User Project',
            'description' => 'Other User Description',
            'user_id' => $user2->id
        ]);

        $response = $this->get('/projects');
        $response->assertStatus(200);
        $response->assertDontSee('Other User Project');
    }
}
