<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        // 1. Rendre id_lieu_dit nullable (0,1) dans la table parcelle
        Schema::table('parcelle', function (Blueprint $table) {
            $table->integer('id_lieu_dit')->nullable()->change();
        });

        // 2. Création de la table pivot projet_quartier (N:N entre projet et lieu_dit)
        Schema::create('projet_quartier', function (Blueprint $table) {
            $table->integer('id_projet');
            $table->integer('id_lieu_dit');

            $table->primary(['id_projet', 'id_lieu_dit']);

            $table->foreign('id_projet')->references('id_projet')->on('projet')->onDelete('cascade');
            $table->foreign('id_lieu_dit')->references('id_lieu_dit')->on('lieu_dit')->onDelete('cascade');
        });

        // 3. Création de la table pivot espace_parcelle (N:N entre parcelle et lieux_publics)
        Schema::create('espace_parcelle', function (Blueprint $table) {
            $table->integer('id_parcelle');
            $table->integer('id_lieu');

            $table->primary(['id_parcelle', 'id_lieu']);

            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle')->onDelete('cascade');
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics')->onDelete('cascade');
        });

        // 4. Suppression de l'ancienne colonne id_parcelle dans lieux_publics (si elle existait)
        Schema::table('lieux_publics', function (Blueprint $table) {
            if (Schema::hasColumn('lieux_publics', 'id_parcelle')) {
                // On retire d'abord la contrainte de clé étrangère (si Laravel l'avait nommée ainsi)
                // DB::statement('ALTER TABLE lieux_publics DROP CONSTRAINT IF EXISTS lieux_publics_id_parcelle_foreign');

                $table->dropColumn('id_parcelle');
            }
        });
    }

    /**
     * Annule les migrations (Rollback).
     */
    public function down(): void
    {
        // On remet la colonne id_parcelle
        Schema::table('lieux_publics', function (Blueprint $table) {
            if (!Schema::hasColumn('lieux_publics', 'id_parcelle')) {
                $table->integer('id_parcelle')->nullable();
                $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            }
        });

        // On supprime les tables pivot
        Schema::dropIfExists('espace_parcelle');
        Schema::dropIfExists('projet_quartier');

        // On remet id_lieu_dit en NOT NULL (1,1)
        Schema::table('parcelle', function (Blueprint $table) {
            $table->integer('id_lieu_dit')->nullable(false)->change();
        });
    }
};