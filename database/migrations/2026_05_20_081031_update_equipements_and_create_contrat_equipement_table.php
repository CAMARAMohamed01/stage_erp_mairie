<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. On retire uniquement la colonne 
        Schema::table('equipement', function (Blueprint $table) {
            $table->dropColumn('id_contrat');
        });// 1.1 On peut aussi supprimer la contrainte de clé étrangère si elle existe
        // Schema::table('equipement', function (Blueprint $table) {
        //     $table->dropForeign(['id_contrat']);
        // });

        // 2. On crée la nouvelle table pivot
        Schema::create('contrat_equipement', function (Blueprint $table) {
            $table->integer('id_contrat');
            $table->integer('id_equipement');

            $table->primary(['id_contrat', 'id_equipement']);

            $table->foreign('id_contrat')->references('id_contrat')->on('contrat')->onDelete('cascade');
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement')->onDelete('cascade');
        });
    }

    public function down()
    {
        // En cas de rollback (php artisan migrate:rollback), on fait l'inverse
        Schema::dropIfExists('contrat_equipement');

        Schema::table('equipement', function (Blueprint $table) {
            $table->integer('id_contrat')->nullable();
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
        });
    }
};