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
        Schema::create('concession_cimetiere', function (Blueprint $table) {
            $table->id('id_concession');
            $table->text('commentaire_concession')->nullable();
            $table->text('beneficiaires_autorises')->nullable();

            $table->unsignedBigInteger('id_emplacement')->nullable();
            $table->unsignedBigInteger('id_contrat')->unique(); // Contrainte UNIQUE

            $table->foreign('id_emplacement')->references('id_emplacement')->on('emplacement_funeraire');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concession_cimetiere');
    }
};