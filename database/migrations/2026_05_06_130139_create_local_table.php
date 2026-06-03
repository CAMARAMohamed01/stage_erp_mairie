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
        Schema::create('local_', function (Blueprint $table) {
            $table->id('id_local');
            $table->string('nom_local', 80);
            $table->decimal('largeur', 4, 2)->nullable();
            $table->decimal('longueur', 4, 2)->nullable();
            $table->decimal('surface_m2', 6, 2)->nullable();
            $table->string('niveau', 50)->nullable();
            $table->string('statut_occupation', 50)->nullable();
            $table->string('ref_article_assurance', 50)->nullable();
            $table->decimal('prime_assurance_ttc', 10, 2)->nullable();
            $table->string('remarque', 255)->nullable();

            $table->unsignedBigInteger('id_lieu')->nullable();
            $table->unsignedBigInteger('id_contrat_assurance')->nullable();
            $table->unsignedBigInteger('id_batiment')->nullable();

            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_contrat_assurance')->references('id_contrat')->on('contrat');
            $table->foreign('id_batiment')->references('id_batiment')->on('batiment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local');
    }
};