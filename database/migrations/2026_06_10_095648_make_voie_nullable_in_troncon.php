<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // On fait sauter la contrainte NOT NULL
        DB::statement('ALTER TABLE troncon ALTER COLUMN id_voie DROP NOT NULL;');
    }

    public function down()
    {
        // En cas de marche arrière, on remet le NOT NULL
        DB::statement('ALTER TABLE troncon ALTER COLUMN id_voie SET NOT NULL;');
    }
};