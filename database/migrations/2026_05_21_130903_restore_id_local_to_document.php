<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('document', function (Blueprint $table) {
            // Si id_local a disparu, on le recrée
            if (!Schema::hasColumn('document', 'id_local')) {
                // IMPORTANT : on remet l'ID avec sa clé étrangère correcte
                $table->foreignId('id_local')->nullable()->constrained('local_', 'id_local');
            }
        });
    }

    public function down()
    {
        // Pas nécessaire pour le moment
    }
};