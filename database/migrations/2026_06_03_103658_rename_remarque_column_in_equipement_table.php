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
        Schema::table('equipement', function (Blueprint $table) {
            // 🚀 Crée la colonne "remarque" en format TEXT (nullable au cas où)
            $table->text('remarque')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipement', function (Blueprint $table) {
            // En cas de retour en arrière, on supprime la colonne
            $table->dropColumn('remarque');
        });
    }
};