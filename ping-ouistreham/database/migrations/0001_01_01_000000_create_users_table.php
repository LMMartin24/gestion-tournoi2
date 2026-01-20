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
            $table->string('name'); // Nom d'affichage ou Prénom + Nom
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- CHAMPS AJOUTÉS POUR LE PROJET ---
            
            // On autorise nullable car l'admin n'a pas forcément de licence
            $table->string('license_number')->unique()->nullable(); 
            $table->string('phone')->nullable();
            
            // Points récupérés via l'API FFTT
            $table->integer('points')->default(500);
            
            // Club actuel du joueur
            $table->string('club')->nullable();

            // Rôles : 'player' (classique), 'coach' (entraîneur), 'admin' (le club)
            $table->enum('role', ['player', 'coach', 'admin'])->default('player');

            // -------------------------------------

            $table->rememberToken();
            $table->timestamps();
        });

        // Le reste (password_reset_tokens et sessions) ne change pas
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
