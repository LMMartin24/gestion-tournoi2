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
            // Utilise foreignId qui gère automatiquement le type BigInt Unsigned
            $table->foreignId('super_table_id')
                ->constrained('super_tables') // Précise le nom de la table si besoin
                ->onDelete('cascade');
            
            $table->string('label');
            $table->decimal('entry_fee', 8, 2);
            $table->integer('points_max');
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
