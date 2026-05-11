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
        Schema::create('equipement', function (Blueprint $table) {
            $table->id('id_equipement');
            $table->string('nom_equipement', 80);
            $table->string('reference_serie', 80)->nullable();
            $table->date('date_achat')->nullable();
            $table->string('duree_garantie_mois', 50)->nullable();
            $table->string('marque', 50)->nullable();
            $table->string('etat_fonctionnement', 50)->nullable();
            $table->string('couleur', 50)->nullable(); // Votre ajout !

            $table->unsignedBigInteger('id_immo')->nullable();
            $table->unsignedBigInteger('id_troncon')->nullable();
            $table->unsignedBigInteger('id_service')->nullable();
            $table->unsignedBigInteger('id_lieu')->nullable();
            $table->unsignedBigInteger('id_parent')->nullable(); // Auto-référence
            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_tiers')->nullable();
            $table->unsignedBigInteger('id_local')->nullable();
            $table->unsignedBigInteger('id_famille'); // NOT NULL

            $table->foreign('id_immo')->references('id_immo')->on('immobilisation_inventaire_');
            $table->foreign('id_troncon')->references('id_troncon')->on('troncon');
            $table->foreign('id_service')->references('id_service')->on('service_mairie');
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_parent')->references('id_equipement')->on('equipement');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_local')->references('id_local')->on('local_');
            $table->foreign('id_famille')->references('id_famille')->on('famille_equipement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipement');
    }
};