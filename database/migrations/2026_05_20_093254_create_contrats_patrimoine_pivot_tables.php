<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Table pivot pour les Bâtiments
        if (!Schema::hasTable('contrat_batiment')) {
            Schema::create('contrat_batiment', function (Blueprint $table) {
                $table->integer('id_batiment');
                $table->integer('id_contrat');
                $table->primary(['id_batiment', 'id_contrat']);
                $table->foreign('id_batiment')->references('id_batiment')->on('batiment')->onDelete('cascade');
                $table->foreign('id_contrat')->references('id_contrat')->on('contrat')->onDelete('cascade');
            });
        }

        // 2. Table pivot pour les Locaux
        if (!Schema::hasTable('contrat_local')) {
            Schema::create('contrat_local', function (Blueprint $table) {
                $table->integer('id_local');
                $table->integer('id_contrat');
                $table->primary(['id_local', 'id_contrat']);
                $table->foreign('id_local')->references('id_local')->on('local_')->onDelete('cascade');
                $table->foreign('id_contrat')->references('id_contrat')->on('contrat')->onDelete('cascade');
            });
        }

        // 3. Table pivot pour les Lieux Publics
        if (!Schema::hasTable('contrat_lieu')) {
            Schema::create('contrat_lieu', function (Blueprint $table) {
                $table->integer('id_lieu');
                $table->integer('id_contrat');
                $table->primary(['id_lieu', 'id_contrat']);
                $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics')->onDelete('cascade');
                $table->foreign('id_contrat')->references('id_contrat')->on('contrat')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('contrat_lieu');
        Schema::dropIfExists('contrat_local');
        Schema::dropIfExists('contrat_batiment');
    }
};