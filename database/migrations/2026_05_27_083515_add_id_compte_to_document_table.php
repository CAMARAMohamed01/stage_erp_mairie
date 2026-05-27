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
        Schema::table('document', function (Blueprint $table) {
            // On ajoute la colonne id_compte (nullable car un doc n'est pas forcément un RIB)
            $table->integer('id_compte')->nullable();

            // On ajoute la contrainte de clé étrangère
            $table->foreign('id_compte')
                ->references('id_compte')->on('compte_bancaire')
                ->onDelete('set null'); // Si on supprime le compte, on garde le doc mais on détache le lien
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document', function (Blueprint $table) {
            // Pour annuler la migration, on retire la clé étrangère PUIS la colonne
            $table->dropForeign(['id_compte']);
            $table->dropColumn('id_compte');
        });
    }
};