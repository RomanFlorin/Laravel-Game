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
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->string('winner')->nullable();
            
            // Hero initial stats
            $table->integer('hero_initial_health');
            $table->integer('hero_initial_strength');
            $table->integer('hero_initial_defence');
            $table->integer('hero_initial_speed');
            $table->decimal('hero_initial_luck', 4, 2);

            // Monster initial stats
            $table->integer('monster_initial_health');
            $table->integer('monster_initial_strength');
            $table->integer('monster_initial_defence');
            $table->integer('monster_initial_speed');
            $table->decimal('monster_initial_luck', 4, 2);

            $table->integer('total_rounds')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
