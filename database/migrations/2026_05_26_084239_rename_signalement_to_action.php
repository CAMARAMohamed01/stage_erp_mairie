<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. On part bien des ANCIENS noms pour aller vers les NOUVEAUX
        Schema::rename('signalement', 'action');
        Schema::rename('historique_signalement', 'historique_action');

        // 2. Renommer les colonnes dans la nouvelle table 'action'
        Schema::table('action', function (Blueprint $table) {
            $table->renameColumn('id_sig', 'id_action');
            $table->renameColumn('statut_signalement', 'statut_action');
        });

        // 3. Renommer les colonnes dans 'historique_action'
        Schema::table('historique_action', function (Blueprint $table) {
            $table->renameColumn('id_hist_sig', 'id_hist_action');
            $table->renameColumn('id_sig', 'id_action');
        });

        // 4. Mettre à jour la clé étrangère dans la table 'intervention'
        Schema::table('intervention', function (Blueprint $table) {
            $table->renameColumn('id_sig', 'id_action');
        });
    }

    public function down()
    {
        Schema::table('intervention', function (Blueprint $table) {
            $table->renameColumn('id_action', 'id_sig');
        });

        Schema::table('historique_action', function (Blueprint $table) {
            $table->renameColumn('id_action', 'id_sig');
            $table->renameColumn('id_hist_action', 'id_hist_sig');
        });

        Schema::table('action', function (Blueprint $table) {
            $table->renameColumn('statut_action', 'statut_signalement');
            $table->renameColumn('id_action', 'id_sig');
        });

        Schema::rename('historique_action', 'historique_signalement');
        Schema::rename('action', 'signalement');
    }
};