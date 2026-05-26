<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. Suppression des anciennes tables transactionnelles redondantes
        Schema::dropIfExists('location_local');
        Schema::dropIfExists('location_equipement');
        Schema::dropIfExists('occupation_lieu_public');

        // 2. Enrichissement de la table pivot 'contrat_local'
        Schema::table('contrat_local', function (Blueprint $table) {
            $table->integer('id_decision')->nullable();
            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_utilisation')->nullable();
            $table->text('etat_lieux_entree')->nullable();
            $table->text('etat_lieux_sortie')->nullable();
            $table->decimal('caution_retenue', 10, 2)->nullable();
            $table->string('statut_ligne', 50)->nullable();

            // Ajout de la contrainte de clé étrangère optionnelle
            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
        });

        // 3. Enrichissement de la table pivot 'contrat_equipement'
        Schema::table('contrat_equipement', function (Blueprint $table) {
            $table->integer('id_decision')->nullable();
            $table->integer('quantite_louee')->nullable();
            $table->integer('quantite_rendue')->nullable();
            $table->string('etat_depart', 100)->nullable();
            $table->string('etat_retour', 100)->nullable();
            $table->decimal('montant_penalite', 10, 2)->nullable();
            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_utilisation')->nullable();
            $table->string('statut_ligne', 50)->nullable();

            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
        });

        // 4. Enrichissement de la table pivot 'contrat_lieu'
        Schema::table('contrat_lieu', function (Blueprint $table) {
            $table->integer('id_decision')->nullable();
            $table->date('date_debut_occupation')->nullable();
            $table->date('date_fin_occupation')->nullable();
            $table->decimal('surface_occupee_m2', 10, 2)->nullable();
            $table->string('usage_specifique', 255)->nullable();
            $table->text('etat_lieux_avant')->nullable();
            $table->text('etat_lieux_apres')->nullable();
            $table->string('statut_ligne', 50)->nullable();

            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
        });
    }

    public function down()
    {
        // En cas de rollback : on retire les colonnes ajoutées
        Schema::table('contrat_lieu', function (Blueprint $table) {
            $table->dropForeign(['id_decision']);
            $table->dropColumn([
                'id_decision',
                'date_debut_occupation',
                'date_fin_occupation',
                'surface_occupee_m2',
                'usage_specifique',
                'etat_lieux_avant',
                'etat_lieux_apres',
                'statut_ligne'
            ]);
        });

        Schema::table('contrat_equipement', function (Blueprint $table) {
            $table->dropForeign(['id_decision']);
            $table->dropColumn([
                'id_decision',
                'quantite_louee',
                'quantite_rendue',
                'etat_depart',
                'etat_retour',
                'montant_penalite',
                'date_debut_utilisation',
                'date_fin_utilisation',
                'statut_ligne'
            ]);
        });

        Schema::table('contrat_local', function (Blueprint $table) {
            $table->dropForeign(['id_decision']);
            $table->dropColumn([
                'id_decision',
                'date_debut_utilisation',
                'date_fin_utilisation',
                'etat_lieux_entree',
                'etat_lieux_sortie',
                'caution_retenue',
                'statut_ligne'
            ]);
        });

    }
};