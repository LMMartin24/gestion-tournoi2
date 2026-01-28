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
            
            // Relation vers le compte qui crée (Coach ou Joueur)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Relation vers le tableau spécifique
            $table->foreignId('sub_table_id')->constrained()->onDelete('cascade');
            
            // --- SNAPSHOT DES DONNÉES JOUEUR ---
            // On stocke ces infos ici pour le Juge-Arbitre, même si l'utilisateur modifie son profil plus tard
            $table->string('player_license');
            $table->string('player_firstname');
            $table->string('player_lastname');
            $table->integer('player_points');

            // --- LOGIQUE MÉTIER ---
            // 'primary' = inscription normale, 'backup' = le joueur veut ce tableau si le premier est plein
            $table->enum('priority', ['primary', 'backup'])->default('primary');
            
            // État géré par le système selon la capacité de la SuperTable
            $table->enum('status', ['confirmed', 'waiting_list', 'cancelled'])->default('waiting_list');
            
            // Check-in final (confirmation de présence la veille ou le matin même)
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