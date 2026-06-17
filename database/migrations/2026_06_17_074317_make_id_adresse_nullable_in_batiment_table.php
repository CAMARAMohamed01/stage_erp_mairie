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
            // Rend la colonne id_adresse optionnelle (0,1)
            $table->integer('id_adresse')->nullable()->change();
        });
    }

    /**
     * Annule les migrations (Rollback).
     */
    public function down(): void
    {
        Schema::table('batiment', function (Blueprint $table) {
            // Remet la contrainte obligatoire (1,1) si besoin
            $table->integer('id_adresse')->nullable(false)->change();
        });
    }
};