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
        Schema::create('dossier_financier', function (Blueprint $table) {
            $table->id('id_dossier');
            $table->string('objet_dossier', 255)->nullable();
            $table->string('numero_titre_recette', 50)->nullable();
            $table->string('numero_devis', 50)->nullable();
            $table->string('numero_engagement', 50)->nullable();
            $table->string('numero_bon_commande', 50)->nullable();
            $table->string('numero_bon_livraison', 50)->nullable();
            $table->string('numero_facture', 50)->nullable();
            $table->string('statut_actuel', 50);
            $table->date('date_constatation_recette')->nullable();
            $table->date('date_emission_titre')->nullable();
            $table->date('date_encaissement')->nullable();
            $table->date('date_reception_devis')->nullable();
            $table->date('date_signature_engagement')->nullable();
            $table->date('date_bon_livraison')->nullable();
            $table->date('date_service_fait')->nullable();
            $table->date('date_reception_facture')->nullable();
            $table->date('date_transmission_compta')->nullable();

            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_tiers')->nullable();

            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_financier');
    }
};