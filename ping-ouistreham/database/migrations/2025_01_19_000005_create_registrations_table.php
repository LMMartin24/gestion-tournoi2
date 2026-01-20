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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            // Celui qui a fait l'action (Joueur lui-même ou son Entraîneur)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('sub_table_id')->constrained()->onDelete('cascade');
            
            // On stocke les infos du joueur au moment de l'inscription (provenant de l'API FFTT)
            $table->string('player_license');
            $table->string('player_firstname');
            $table->string('player_lastname');
            $table->integer('player_points');

            // Gestion de la logique de secours
            // 'primary' = l'un des 2 tableaux max choisis. 'backup' = tableau de secours
            $table->enum('priority', ['primary', 'backup'])->default('primary');
            
            // État de l'inscription
            $table->enum('status', ['confirmed', 'waiting_list', 'cancelled'])->default('waiting_list');
            
            // Pour le mail de clôture (confirmation de présence)
            $table->boolean('presence_confirmed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
