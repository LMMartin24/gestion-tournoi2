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
        Schema::create('sub_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_table_id')->constrained()->onDelete('cascade');
            $table->string('label'); // ex: "Tableau H - Moins de 1200 pts"
            $table->integer('points_min')->default(500);
            $table->integer('points_max');
            $table->integer('max_players'); // Nombre max d'inscrits autorisés
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_tables');
    }
};
