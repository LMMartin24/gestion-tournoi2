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
            $table->string('name');
            $table->date('date');
            $table->string('location');
            $table->string('contact_email')->nullable()->change();
            $table->timestamp('registration_deadline'); // Date de clôture
            $table->boolean('is_published')->default(false); // Pour préparer le tournoi avant de le montrer
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
