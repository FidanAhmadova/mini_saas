<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'max_projects',
        'max_team_members',
        'has_api_access',
        'has_real_time_notifications',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_api_access' => 'boolean',
        'has_real_time_notifications' => 'boolean',
        'is_active' => 'boolean',
    ];

    // İstifadəçilərin bu planla abunəlikləri
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Planın limitsiz layihəyə icazəsi varmı?
    public function hasUnlimitedProjects()
    {
        return $this->max_projects === -1;
    }

    // Planın limitsiz komanda üzvlərinə icazəsi varmı?
    public function hasUnlimitedTeamMembers()
    {
        return $this->max_team_members === -1;
    }

    // Pulsuz planmı?
    public function isFree()
    {
        return $this->price == 0;
    }

    // Plana görə qiymət formatla
    public function getFormattedPriceAttribute()
    {
        if ($this->isFree()) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2) . '/' . strtolower($this->currency);
    }
}
