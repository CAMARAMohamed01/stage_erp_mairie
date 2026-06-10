<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('intervention', function (Blueprint $table) {
            // Ajout des colonnes en nullable (car une intervention n'est pas forcément sur les deux à la fois)
            $table->unsignedInteger('id_batiment')->nullable();
            $table->unsignedInteger('id_lieu')->nullable();

            // Création des contraintes de clés étrangères
            $table->foreign('id_batiment')->references('id_batiment')->on('batiment')->nullOnDelete();
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('intervention', function (Blueprint $table) {
            // En cas de rollback, on supprime d'abord les contraintes, puis les colonnes
            $table->dropForeign(['id_batiment']);
            $table->dropForeign(['id_lieu']);

            $table->dropColumn(['id_batiment', 'id_lieu']);
        });
    }
};