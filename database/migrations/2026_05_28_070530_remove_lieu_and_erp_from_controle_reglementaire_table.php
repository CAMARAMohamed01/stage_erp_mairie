<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('controle_reglementaire', function (Blueprint $table) {
            // 1. On supprime d'abord les contraintes de clés étrangères
            // (L'utilisation d'un tableau dit à Laravel de deviner le nom de la contrainte)
            $table->dropForeign(['id_lieu']);
            $table->dropForeign(['id_type_erp']);

            // 2. Ensuite, on supprime physiquement les colonnes
            $table->dropColumn('id_lieu');
            $table->dropColumn('id_type_erp');
        });
    }

    public function down()
    {
        Schema::table('controle_reglementaire', function (Blueprint $table) {
            // En cas de rollback (annulation), on remet les colonnes
            $table->integer('id_lieu')->nullable();
            $table->integer('id_type_erp')->nullable();

            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_type_erp')->references('id_type_erp')->on('type_erp');
        });
    }
};