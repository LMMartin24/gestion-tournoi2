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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // --- CHAMPS DE BASE LARAVEL ---
            // 'name' peut servir de pseudo ou être rempli par 'Prénom Nom' via le code
            $table->string('name'); 
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- CHAMPS IDENTITÉ (Format SPID/FFTT) ---
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            
            // Licence unique (nullable pour les admins/coachs non joueurs)
            $table->string('license_number')->unique()->nullable(); 
            $table->string('phone')->nullable();
            
            // --- DONNÉES SPORTIVES ---
            $table->integer('points')->default(500);
            $table->string('club')->nullable();

            // --- SYSTÈME DE RÔLES & VALIDATION ---
            /**
             * player      : Joueur standard
             * coach       : Entraîneur (gestion de groupe)
             * admin       : Organisateur de club (crée des tournois)
             * super_admin : Toi (gestionnaire de la plateforme nationale)
             */
            $table->enum('role', ['player', 'coach', 'admin', 'super_admin'])->default('player');

            // Sécurité SaaS : Un compte 'admin' doit être validé par toi 
            // pour pouvoir publier des tournois officiels sur la plateforme
            $table->boolean('is_verified_organizer')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });

        // Tables techniques (Breeze/Jetstream standard)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};