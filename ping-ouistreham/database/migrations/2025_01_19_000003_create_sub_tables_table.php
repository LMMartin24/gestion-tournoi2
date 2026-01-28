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
            
            // Relation avec le bloc horaire (SuperTable)
            $table->foreignId('super_table_id')
                ->constrained('super_tables')
                ->onDelete('cascade');
            
            // Infos du tableau
            $table->string('label');             // ex: "Tableau H"
            $table->decimal('entry_fee', 8, 2);  // ex: 8.50
            
            // Restrictions de niveau (FFTT)
            // On garde points_min pour pouvoir faire des tableaux "1300 à 1599"
            $table->integer('points_min')->default(500); 
            $table->integer('points_max');               // ex: 1299
            
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