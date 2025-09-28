<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function projects() {
        return $this->belongsToMany(Project::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Abunəliklər
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Aktiv abunəlik
    public function activeSubscription()
    {
        return $this->subscriptions()
                    ->where('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('ends_at')
                              ->orWhere('ends_at', '>', now());
                    })
                    ->with('plan')
                    ->first();
    }

    // İstifadəçinin hazırkı planı
    public function currentPlan()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->plan : Plan::where('slug', 'free')->first();
    }

    // Plan limitlərini yoxla
    public function canCreateProject()
    {
        $plan = $this->currentPlan();
        if ($plan->hasUnlimitedProjects()) {
            return true;
        }
        
        $projectCount = $this->projects()->count();
        return $projectCount < $plan->max_projects;
    }

    // Komanda üzvü əlavə edə bilərmi?
    public function canInviteTeamMember($projectId)
    {
        $plan = $this->currentPlan();
        if ($plan->hasUnlimitedTeamMembers()) {
            return true;
        }
        
        $project = $this->projects()->find($projectId);
        if (!$project) {
            return false;
        }
        
        $memberCount = $project->members()->count();
        return $memberCount < $plan->max_team_members;
    }

    // API giriş icazəsi varmı?
    public function hasApiAccess()
    {
        $plan = $this->currentPlan();
        return $plan->has_api_access;
    }

    // Real-time bildiriş icazəsi varmı?
    public function hasRealTimeNotifications()
    {
        $plan = $this->currentPlan();
        return $plan->has_real_time_notifications;
    }
}
