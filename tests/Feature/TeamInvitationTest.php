<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\TeamInvitation;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_owner_can_invite_team_member()
    {
        $owner = User::factory()->create();
        $this->actingAs($owner);

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

        // Owner-ə subscription ver
        Subscription::create([
            'user_id' => $owner->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $owner->id
        ]);

        // Owner-ı project-ə member kimi əlavə et
        $project->members()->attach($owner->id, ['role' => 'owner']);

        $response = $this->post("/team/invite/{$project->id}", [
            'email' => 'test@example.com',
            'role' => 'member'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('team_invitations', [
            'project_id' => $project->id,
            'email' => 'test@example.com',
            'role' => 'member',
            'status' => 'pending'
        ]);
    }

    public function test_non_owner_cannot_invite_team_member()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $this->actingAs($member);

        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $owner->id
        ]);

        $response = $this->post("/team/invite/{$project->id}", [
            'email' => 'test@example.com',
            'role' => 'member'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_accept_invitation()
    {
        $owner = User::factory()->create();
        $invitedUser = User::factory()->create(['email' => 'test@example.com']);

        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $owner->id
        ]);

        $invitation = TeamInvitation::create([
            'project_id' => $project->id,
            'invited_by' => $owner->id,
            'email' => 'test@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'status' => 'pending',
            'expires_at' => now()->addDays(7)
        ]);

        $this->actingAs($invitedUser);

        $response = $this->get("/team/accept/{$invitation->token}");
        $response->assertRedirect("/projects/{$project->id}");

        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $invitedUser->id,
            'role' => 'member'
        ]);

        $this->assertDatabaseHas('team_invitations', [
            'id' => $invitation->id,
            'status' => 'accepted'
        ]);
    }

    public function test_user_can_decline_invitation()
    {
        $owner = User::factory()->create();
        $invitedUser = User::factory()->create(['email' => 'test@example.com']);

        $project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'user_id' => $owner->id
        ]);

        $invitation = TeamInvitation::create([
            'project_id' => $project->id,
            'invited_by' => $owner->id,
            'email' => 'test@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'status' => 'pending',
            'expires_at' => now()->addDays(7)
        ]);

        $this->actingAs($invitedUser);

        $response = $this->get("/team/decline/{$invitation->token}");
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('team_invitations', [
            'id' => $invitation->id,
            'status' => 'declined'
        ]);
    }
}
