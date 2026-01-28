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
        Schema::create('sub_table_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_table_id')->constrained()->onDelete('cascade');
            
            // --- NOUVELLES COLONNES STRATÉGIQUES ---

            // Qui a fait l'inscription ? (L'id du joueur ou l'id du coach)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // 'primary' = l'un des tableaux choisis. 'backup' = tableau de secours
            $table->enum('priority', ['primary', 'backup'])->default('primary');
            
            // État de l'inscription pour ce tableau précis
            $table->enum('status', ['confirmed', 'waiting_list', 'cancelled'])->default('waiting_list');

            // Confirmation finale (utile pour le pointage le jour J)
            $table->boolean('presence_confirmed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_table_user');
    }
};