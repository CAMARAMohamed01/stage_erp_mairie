<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('action', function (Blueprint $table) {
            // 1. Création des colonnes (nullable car un signalement n'est pas forcément lié aux deux en même temps)
            $table->integer('id_lieu')->nullable();
            $table->integer('id_batiment')->nullable();

            // 2. Création des contraintes de clés étrangères
            $table->foreign('id_lieu')
                ->references('id_lieu')
                ->on('lieux_publics')
                ->onDelete('set null'); // Si on supprime le lieu, on garde l'action mais on met l'ID à null

            $table->foreign('id_batiment')
                ->references('id_batiment')
                ->on('batiment')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action', function (Blueprint $table) {
            // 1. On supprime d'abord les contraintes (toujours respecter cet ordre)
            $table->dropForeign(['id_lieu']);
            $table->dropForeign(['id_batiment']);

            // 2. On supprime les colonnes
            $table->dropColumn(['id_lieu', 'id_batiment']);
        });
    }
};