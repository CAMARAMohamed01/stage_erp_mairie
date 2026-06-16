<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            // On ajoute la colonne en nullable pour ne pas bloquer avec tes lieux déjà importés
            $table->integer('id_adresse')->nullable();

            // On crée la clé étrangère vers la table Adresse
            $table->foreign('id_adresse')
                ->references('id_adresse')
                ->on('Adresse')
                ->onDelete('set null'); // Si l'adresse est supprimée, on vide juste la case du lieu
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            // On supprime d'abord la contrainte, puis la colonne
            $table->dropForeign(['id_adresse']);
            $table->dropColumn('id_adresse');
        });
    }
};