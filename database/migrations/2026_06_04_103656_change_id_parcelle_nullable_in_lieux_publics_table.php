<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            // Rend la colonne id_parcelle optionnelle
            $table->unsignedInteger('id_parcelle')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lieux_publics', function (Blueprint $table) {
            $table->unsignedInteger('id_parcelle')->nullable(false)->change();
        });
    }
};