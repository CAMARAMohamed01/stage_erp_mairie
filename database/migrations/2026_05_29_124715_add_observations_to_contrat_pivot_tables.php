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
        // 🏢 1. MISE À NIVEAU COMPLÈTE DE CONTRAT_BATIMENT
        Schema::table('contrat_batiment', function (Blueprint $table) {
            $table->integer('id_decision')->nullable();
            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_utilisation')->nullable();
            $table->text('etat_lieux_entree')->nullable();
            $table->text('etat_lieux_sortie')->nullable();
            $table->decimal('caution_retenue', 10, 2)->nullable();
            $table->date('date_modification')->nullable();
            $table->string('statut_ligne', 50)->default('En cours');
            $table->text('observations')->nullable(); // En plus des colonnes d'état

            // Contrainte de clé étrangère vers les actes administratifs
            $table->foreign('id_decision')
                ->references('id_decision')
                ->on('decision_administratif')
                ->onDelete('set null');
        });

        // 🚚 2. AJOUT DES OBSERVATIONS SUR CONTRAT_EQUIPEMENT
        Schema::table('contrat_equipement', function (Blueprint $table) {
            $table->text('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nettoyage de contrat_batiment
        Schema::table('contrat_batiment', function (Blueprint $table) {
            $table->dropForeign(['id_decision']);
            $table->dropColumn([
                'id_decision',
                'date_debut_utilisation',
                'date_fin_utilisation',
                'etat_lieux_entree',
                'etat_lieux_sortie',
                'caution_retenue',
                'date_modification',
                'statut_ligne',
                'observations'
            ]);
        });

        // Nettoyage de contrat_equipement
        Schema::table('contrat_equipement', function (Blueprint $table) {
            $table->dropColumn('observations');
        });
    }
};