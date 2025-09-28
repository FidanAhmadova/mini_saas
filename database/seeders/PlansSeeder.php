<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        // Free Plan
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'description' => 'Perfect for getting started with basic project management.',
            'price' => 0.00,
            'currency' => 'USD',
            'max_projects' => 2,
            'max_team_members' => 3,
            'has_api_access' => false,
            'has_real_time_notifications' => false,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Pro Plan
        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'For teams that need unlimited projects and advanced features.',
            'price' => 9.99,
            'currency' => 'USD',
            'max_projects' => -1, // unlimited
            'max_team_members' => -1, // unlimited
            'has_api_access' => true,
            'has_real_time_notifications' => true,
            'is_active' => true,
            'sort_order' => 2
        ]);
    }
}