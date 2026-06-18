<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projet_voie', function (Blueprint $table) {
            // Création des colonnes (integer pour correspondre au type SERIAL/INTEGER de ta BDD)
            $table->integer('id_projet');
            $table->integer('id_voie');

            // Définition de la clé primaire composite (l'association des deux est unique)
            $table->primary(['id_projet', 'id_voie']);

            // Création des clés étrangères avec suppression en cascade
            // (Si un projet est supprimé, on supprime automatiquement ses liaisons avec les voies)
            $table->foreign('id_projet')
                ->references('id_projet')->on('projet')
                ->onDelete('cascade');

            $table->foreign('id_voie')
                ->references('id_voie')->on('voie')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('projet_voie');
    }
};