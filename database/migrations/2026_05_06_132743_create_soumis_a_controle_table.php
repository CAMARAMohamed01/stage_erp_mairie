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
        Schema::create('soumis_a_controle', function (Blueprint $table) {
            $table->unsignedBigInteger('id_controle');
            $table->unsignedBigInteger('id_equipement');
            $table->primary(['id_controle', 'id_equipement']);
            $table->foreign('id_controle')->references('id_controle')->on('controle_reglementaire');
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement');
            $table->date('date_controle')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soumis_a_controle');
    }
};