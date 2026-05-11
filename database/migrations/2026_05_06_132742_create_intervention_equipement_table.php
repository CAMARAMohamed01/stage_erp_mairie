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
        Schema::create('intervention_equipement', function (Blueprint $table) {
            $table->unsignedBigInteger('id_int');
            $table->unsignedBigInteger('id_equipement');
            $table->primary(['id_int', 'id_equipement']);
            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_equipement');
    }
};