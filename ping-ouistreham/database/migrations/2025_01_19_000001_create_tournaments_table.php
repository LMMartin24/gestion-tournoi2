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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();

            // L'organisateur (doit avoir le rôle 'admin' ou 'super_admin')
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Infos générales
            $table->string('name');
            $table->string('slug')->unique(); // ex: tournoi-national-caen-2026
            $table->date('date');
            $table->string('location'); // Adresse lisible (ex: "Gymnase Legoupil, Ouistreham")
            
            // --- GEOLOCALISATION ---
            // Utilisé pour le calcul de distance (ex: "Tournois à moins de 50km")
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // --- CONTACT & LOGISTIQUE ---
            $table->string('contact_email')->nullable();
            $table->timestamp('registration_deadline'); // Date et heure de fin des inscriptions
            
            // --- FILTRAGE & RESTRICTIONS ---
            // Permet d'exclure le tournoi des recherches si le joueur a trop de points
            $table->integer('max_points_allowed')->nullable(); 
            
            // --- WORKFLOW DE VALIDATION ---
            /**
             * pending  : Créé par le club, en attente de validation SuperAdmin
             * accepted : Validé par toi, apparaît dans les recherches
             * rejected : Refusé (motif à préciser éventuellement)
             */
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            // Visibilité publique (indépendant du statut, géré par l'organisateur)
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};