<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tiers_morale', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tiers')->primary(); // Clé primaire
            $table->string('raison_sociale', 150)->nullable();
            $table->string('siret', 14)->nullable();
            $table->string('numero_tva_intra', 30)->nullable();
            $table->string('alias_tiers', 10)->nullable();
            $table->string('nom_contact', 100)->nullable();
            $table->string('num_compte_client', 50)->nullable();

            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers_morale');
    }
};