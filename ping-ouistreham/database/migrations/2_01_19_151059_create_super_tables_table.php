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
        Schema::create('super_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->string('label'); // ex: "Matinée du Samedi"
            $table->time('start_time'); // Heure de départ commune
            $table->integer('max_players_total'); // Limite globale pour ce créneau
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_tables');
    }
};
