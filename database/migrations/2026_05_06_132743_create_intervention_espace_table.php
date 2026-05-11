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
        Schema::create('intervention_espace', function (Blueprint $table) {
            $table->unsignedBigInteger('id_int');
            $table->unsignedBigInteger('id_lieu');
            $table->primary(['id_int', 'id_lieu']);
            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_espace');
    }
};