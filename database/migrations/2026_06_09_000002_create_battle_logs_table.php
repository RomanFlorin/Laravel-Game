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
        Schema::create('battle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battle_id')->constrained('battles')->onDelete('cascade');
            $table->integer('round_number');
            $table->string('attacker');
            $table->string('defender');
            $table->text('action_description');
            $table->json('skills_triggered')->nullable();
            $table->integer('damage_dealt');
            $table->integer('defender_health_after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battle_logs');
    }
};
