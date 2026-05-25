<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajout de la colonne spatiale Point pour la table batiment
        DB::statement('ALTER TABLE batiment ADD COLUMN geom_batiment geometry(Point, 4326);');

        // Ajout de la colonne spatiale Point pour la table lieu (ou lieux_publics selon ton nom exact)
        DB::statement('ALTER TABLE lieux_publics ADD COLUMN geom_lieu geometry(Point, 4326);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression des colonnes en cas de rollback
        DB::statement('ALTER TABLE batiment DROP COLUMN IF EXISTS geom_batiment;');
        DB::statement('ALTER TABLE lieux_publics DROP COLUMN IF EXISTS geom_lieu;');
    }
};