<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Exécute la migration (Ajout du champ).
     */
    public function up(): void
    {
        Schema::table('controle_reglementaire', function (Blueprint $table) {
            // Ajoute le champ numero_controle (type VARCHAR 50, autorise les valeurs nulles au cas où)
            // after() permet de le placer juste après l'ID pour que ce soit propre dans ta BDD
            $table->string('numero_controle', 50)->nullable()->after('id_controle');
        });
    }

    /**
     * Annule la migration (Suppression du champ en cas de rollback).
     */
    public function down(): void
    {
        Schema::table('controle_reglementaire', function (Blueprint $table) {
            $table->dropColumn('numero_controle');
        });
    }
};