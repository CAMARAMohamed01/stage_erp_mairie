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
        Schema::create('commune_partenaire', function (Blueprint $table) {
        $table->id('id_commune');
        $table->string('nom_commune', 80);
        $table->string('code_postal', 5)->nullable();
        $table->string('siret_mairie', 14)->nullable();
        $table->string('email_contact', 100)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commune_partenaire');
    }
};
