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
        Schema::table('type_erp_controle', function (Blueprint $table) {
            // Ajoute la colonne date_controle de type Date et nullable
            $table->date('date_controle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_erp_controle', function (Blueprint $table) {
            $table->dropColumn('date_controle');
        });
    }
};