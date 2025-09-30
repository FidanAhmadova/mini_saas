<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Pro
            $table->string('slug')->unique(); // free, pro
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->default(0); // 0.00 for Free, 9.99 for Pro
            $table->string('currency', 3)->default('USD');
            $table->integer('max_projects')->default(2); // 2 for Free, -1 for unlimited
            $table->integer('max_team_members')->default(5); // 5 for Free, -1 for unlimited
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_real_time_notifications')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
