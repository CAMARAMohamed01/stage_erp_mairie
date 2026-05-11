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
        Schema::create('lieux_publics', function (Blueprint $table) {
            $table->id('id_lieu');
            $table->string('nom_lieu', 80);
            $table->string('typologie_lieu', 80)->nullable();
            $table->decimal('surface_m2', 8, 2)->nullable();
            $table->time('horaire_ouverture')->nullable();
            $table->time('horaire_fermeture')->nullable();

            $table->unsignedBigInteger('id_decision_reglement')->nullable();
            $table->unsignedBigInteger('id_tiers'); // NOT NULL
            $table->unsignedBigInteger('id_batiment')->nullable();
            $table->unsignedBigInteger('id_type_erp')->nullable();
            $table->unsignedBigInteger('id_parcelle'); // NOT NULL
            $table->unsignedBigInteger('id_immo')->unique(); // Contrainte UNIQUE

            $table->foreign('id_decision_reglement')->references('id_decision')->on('decision_administratif');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_batiment')->references('id_batiment')->on('batiment');
            $table->foreign('id_type_erp')->references('id_type_erp')->on('type_erp');
            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_immo')->references('id_immo')->on('immobilisation_inventaire_');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lieux_publics');
    }
};