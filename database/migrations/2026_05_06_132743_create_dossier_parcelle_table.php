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
        Schema::create('dossier_parcelle', function (Blueprint $table) {
            $table->unsignedBigInteger('id_parcelle');
            $table->unsignedBigInteger('id_dossier');
            $table->primary(['id_parcelle', 'id_dossier']);
            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_dossier')->references('id_dossier')->on('dossier_urba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_parcelle');
    }
};