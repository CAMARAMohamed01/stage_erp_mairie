<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE secteur ALTER COLUMN geom_secteur TYPE GEOMETRY(MultiPolygon, 4326) USING ST_SetSRID(ST_Multi(geom_secteur), 4326);');

        // ⚠️ La correction est ici : "Zone" entre guillemets
        DB::statement('ALTER TABLE "Zone" ALTER COLUMN geom_zone TYPE GEOMETRY(MultiPolygon, 4326) USING ST_SetSRID(ST_Multi(geom_zone), 4326);');

        DB::statement('ALTER TABLE parcelle ALTER COLUMN geom_parcelle TYPE GEOMETRY(MultiPolygon, 4326) USING ST_SetSRID(ST_Multi(geom_parcelle), 4326);');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE secteur ALTER COLUMN geom_secteur TYPE GEOMETRY;');

        // ⚠️ Ici aussi pour le rollback
        DB::statement('ALTER TABLE "Zone" ALTER COLUMN geom_zone TYPE GEOMETRY;');

        DB::statement('ALTER TABLE parcelle ALTER COLUMN geom_parcelle TYPE GEOMETRY;');
    }
};