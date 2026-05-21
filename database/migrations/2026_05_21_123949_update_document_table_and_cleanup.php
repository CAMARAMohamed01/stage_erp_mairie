<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('document', function (Blueprint $table) {
            // 1. Ajout de id_batiment (sécurisé)
            if (!Schema::hasColumn('document', 'id_batiment')) {
                $table->foreignId('id_batiment')->nullable()->constrained('batiment', 'id_batiment');
            }

            // 2. Suppression uniquement des doublons identifiés
            // Remplace 'id_local_1' par le nom exact trouvé avec la commande tinker
            if (Schema::hasColumn('document', 'id_local_1')) {
                $table->dropColumn('id_local_1');
            }


        });
    }



    public function down()
    {
        Schema::table('document', function (Blueprint $table) {
            // Rollback : supprimer la FK et remettre id_local
            $table->dropForeign(['id_batiment']);
            $table->dropColumn('id_batiment');
            $table->integer('id_local')->nullable();
        });
    }
};