<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            // Rend la colonne nullable (il faut avoir installé le package "doctrine/dbal" pour utiliser ->change())
            $table->integer('id_immo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            $table->integer('id_immo')->nullable(false)->change();
        });
    }
};