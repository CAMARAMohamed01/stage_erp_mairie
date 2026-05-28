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
        // 1. Correction de la table dossier_financier (id_dossier -> id_dossier_f)
        if (Schema::hasTable('dossier_financier')) {
            Schema::table('dossier_financier', function (Blueprint $table) {
                if (Schema::hasColumn('dossier_financier', 'id_dossier')) {
                    $table->renameColumn('id_dossier', 'id_dossier_f');
                }
            });
        }

        // 2. Correction de la table ligne_financiere_facture_ (id_dossier -> id_dossier_f)
        if (Schema::hasTable('ligne_financiere_facture_')) {
            Schema::table('ligne_financiere_facture_', function (Blueprint $table) {
                if (Schema::hasColumn('ligne_financiere_facture_', 'id_dossier')) {
                    $table->renameColumn('id_dossier', 'id_dossier_f');
                }
            });
        }

        // 3. Correction de la table document (id_dossier_1 -> id_dossier_f)
        if (Schema::hasTable('document')) {
            Schema::table('document', function (Blueprint $table) {
                if (Schema::hasColumn('document', 'id_dossier_1')) {
                    $table->renameColumn('id_dossier_1', 'id_dossier_f');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('document')) {
            Schema::table('document', function (Blueprint $table) {
                if (Schema::hasColumn('document', 'id_dossier_f')) {
                    $table->renameColumn('id_dossier_f', 'id_dossier_1');
                }
            });
        }

        if (Schema::hasTable('ligne_financiere_facture_')) {
            Schema::table('ligne_financiere_facture_', function (Blueprint $table) {
                if (Schema::hasColumn('ligne_financiere_facture_', 'id_dossier_f')) {
                    $table->renameColumn('id_dossier_f', 'id_dossier');
                }
            });
        }

        if (Schema::hasTable('dossier_financier')) {
            Schema::table('dossier_financier', function (Blueprint $table) {
                if (Schema::hasColumn('dossier_financier', 'id_dossier_f')) {
                    $table->renameColumn('id_dossier_f', 'id_dossier');
                }
            });
        }
    }
};