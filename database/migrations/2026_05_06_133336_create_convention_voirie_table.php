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
        Schema::create('convention_voirie', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_troncon');
            $table->string('type_droit', 100)->nullable();
            $table->date('date_debut_convention')->nullable();
            $table->date('date_fin_convention')->nullable();

            $table->primary(['id_contrat', 'id_troncon']);
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_troncon')->references('id_troncon')->on('troncon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convention_voirie');
    }
};