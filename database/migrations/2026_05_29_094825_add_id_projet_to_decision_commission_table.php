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
        Schema::table('decision_commission', function (Blueprint $table) {
            // 1. On ajoute la colonne id_projet (nullable pour ne pas bloquer les anciennes lignes)
            // On utilise integer() car ton script SQL utilise 'SERIAL' (qui est un Int 4 octets sous Postgres)
            $table->integer('id_projet')->nullable()->after('commentaire_elus');

            // 2. On déclare la contrainte de clé étrangère
            $table->foreign('id_projet')
                ->references('id_projet')
                ->on('projet')
                ->onDelete('set null'); // Sécurité : si on supprime un projet, la décision reste mais passe à null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decision_commission', function (Blueprint $table) {
            // Pour faire machine arrière proprement, on retire d'abord la clé étrangère puis la colonne
            $table->dropForeign(['id_projet']);
            $table->dropColumn('id_projet');
        });
    }
};