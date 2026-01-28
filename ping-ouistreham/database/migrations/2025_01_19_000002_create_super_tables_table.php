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
            
            // Lien vers le tournoi parent
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            
            // Identification du bloc
            $table->string('name');             // ex: "Bloc Samedi Matin" ou "Série 1"
            
            // Timing
            // On ajoute la date car un tournoi national se joue souvent sur 2 ou 3 jours
            $table->date('date');               
            $table->time('start_time');         // ex: 09:00
            
            // Capacité et Logistique
            // max_players permet de gérer la règle d'exclusion : 
            // la somme des inscrits dans les sub_tables ne peut pas dépasser ce nombre.
            $table->integer('max_players');     
            
            // Optionnel : un petit texte pour l'organisation (ex: "Tableaux de 500 à 1200 pts")
            $table->text('description')->nullable(); 

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