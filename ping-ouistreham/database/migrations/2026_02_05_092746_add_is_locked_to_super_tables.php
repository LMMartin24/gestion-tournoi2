<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('super_tables', function (Blueprint $table) {
            // On ajoute le champ 'is_locked' (booléen)
            // Par défaut à 'false' pour ne pas bloquer les tableaux existants
            $table->boolean('is_locked')->default(false)->after('max_players');
        });
    }

    public function down(): void
    {
        Schema::table('super_tables', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};