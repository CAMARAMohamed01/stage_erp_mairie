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
        Schema::create('ouvrage_partage', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ouvrage');
            $table->unsignedBigInteger('id_commune');
            $table->primary(['id_ouvrage', 'id_commune']);
            $table->foreign('id_ouvrage')->references('id_ouvrage')->on('ouvrage');
            $table->foreign('id_commune')->references('id_commune')->on('commune_partenaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouvrage_partage');
    }
};