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
        Schema::create('immobilisation_inventaire_', function (Blueprint $table) {
            $table->id('id_immo');
            $table->string('num_inventaire', 50)->unique();
            $table->string('libelle_comptable', 255);
            $table->decimal('valeur_achat', 10, 2)->nullable();
            $table->date('date_acquisition')->nullable();
            $table->boolean('est_amortissable')->nullable();
            $table->date('date_sortie')->nullable();
            $table->string('motif_sortie', 100)->nullable();
            $table->decimal('valeur_revente', 12, 2)->nullable();

            $table->unsignedBigInteger('id_ligne_vente')->nullable();
            $table->unsignedBigInteger('id_ligne_achat')->nullable();

            $table->foreign('id_ligne_vente')->references('id_ligne')->on('ligne_financiere_facture_');
            $table->foreign('id_ligne_achat')->references('id_ligne')->on('ligne_financiere_facture_');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immobilisation_inventaire');
    }
};