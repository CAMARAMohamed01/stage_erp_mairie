<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('compte_bancaire', function (Blueprint $table) {
            // Passe les colonnes en TEXT pour accueillir les chaînes cryptées
            $table->text('iban')->change();
            $table->text('bic')->change();
            $table->text('rib')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('compte_bancaire', function (Blueprint $table) {
            // Retour aux types d'origine si nécessaire
            $table->string('iban', 34)->change();
            $table->string('bic', 11)->change();
            $table->string('rib', 50)->nullable()->change();
        });
    }
};