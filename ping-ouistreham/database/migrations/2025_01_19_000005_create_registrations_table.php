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
            
            // LE JOUEUR (Indispensable pour ton erreur SQL)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // L'AUTEUR (Coach ou Joueur)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('sub_table_id')->constrained()->onDelete('cascade');
            
            // SNAPSHOTS
            $table->string('player_license');
            $table->string('player_firstname');
            $table->string('player_lastname');
            $table->integer('player_points');

            // LOGIQUE MÉTIER
            $table->enum('priority', ['primary', 'backup'])->default('primary');
            $table->enum('status', ['confirmed', 'waiting_list', 'cancelled'])->default('waiting_list');
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