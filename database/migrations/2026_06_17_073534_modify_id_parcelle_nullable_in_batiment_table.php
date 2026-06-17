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
        Schema::table('batiment', function (Blueprint $table) {
            // Permet à la colonne id_parcelle de devenir optionnelle (0,1)
            $table->integer('id_parcelle')->nullable()->change();
        });
    }

    /**
     * Annule les migrations (Rollback).
     */
    public function down(): void
    {
        Schema::table('batiment', function (Blueprint $table) {
            // Remet la contrainte de saisie obligatoire (1,1)
            $table->integer('id_parcelle')->nullable(false)->change();
        });
    }
};