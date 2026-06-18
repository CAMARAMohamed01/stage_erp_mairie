<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('action', function (Blueprint $table) {
            // Ajout de la colonne (integer classique car SERIAL dans ta DB)
            $table->integer('id_lieu_dit')->nullable();

            // Création de la clé étrangère
            $table->foreign('id_lieu_dit')
                ->references('id_lieu_dit')->on('lieu_dit')
                ->onDelete('set null'); // Sécurité : si on supprime un lieu-dit, on garde l'action mais on vide le champ
        });
    }

    public function down()
    {
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign(['id_lieu_dit']);
            $table->dropColumn('id_lieu_dit');
        });
    }
};