<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlist_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['movie', 'series'])->default('movie');
            $table->enum('status', ['pending', 'watching', 'completed', 'dropped'])->default('pending');
            $table->string('genre')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('platform')->nullable();           // Netflix, HBO, etc.
            $table->unsignedTinyInteger('rating')->nullable(); // 1-10
            $table->unsignedSmallInteger('current_episode')->nullable();  // for series
            $table->unsignedSmallInteger('total_episodes')->nullable();   // for series
            $table->unsignedSmallInteger('seasons')->nullable();          // for series
            $table->text('notes')->nullable();
            $table->string('poster_url')->nullable();
            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_items');
    }
};
