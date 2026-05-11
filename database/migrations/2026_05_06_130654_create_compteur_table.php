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
        Schema::create('compteur', function (Blueprint $table) {
            $table->id('id_compteur');
            $table->string('point_comptage', 30);
            $table->string('numero_compteur', 30)->unique()->nullable();
            $table->string('type_reseau', 80);
            $table->boolean('dessert_tout_le_batiment')->nullable();
            $table->string('unite_mesure', 10)->nullable();
            $table->date('date_pose')->nullable();
            $table->date('date_arret')->nullable();
            $table->string('observations', 255)->nullable();
            $table->string('localisation_vanne_arret', 255)->nullable();

            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_compteur_principal')->nullable(); // Auto-référence
            $table->unsignedBigInteger('id_local'); // NOT NULL

            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_compteur_principal')->references('id_compteur')->on('compteur');
            $table->foreign('id_local')->references('id_local')->on('local_');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compteur');
    }
};