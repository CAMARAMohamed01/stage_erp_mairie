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
        Schema::create('troncon', function (Blueprint $table) {
            $table->id('id_troncon');
            $table->string('numero_troncon', 150)->unique()->nullable();
            $table->string('nom_portion', 100)->nullable();
            $table->decimal('pk_debut', 10, 2)->nullable();
            $table->decimal('pk_fin', 10, 2)->nullable();
            $table->string('type_revetement', 50)->nullable();
            $table->date('date_dernier_goudronnage')->nullable();
            $table->geometry('trace_geo')->nullable();
            $table->string('repere_physique_debut', 100)->nullable();
            $table->string('repere_physique_fin', 100)->nullable();
            $table->string('etat_physique', 50)->nullable();
            $table->string('gabarit_accessibilite', 50)->nullable();
            $table->string('paysage_environnement', 50)->nullable();

            $table->unsignedBigInteger('id_zone')->nullable();
            $table->unsignedBigInteger('id_ouvrage_lie')->nullable();
            $table->unsignedBigInteger('id_ouvrage_fin')->nullable();
            $table->unsignedBigInteger('id_ouvrage_debut')->nullable();
            $table->unsignedBigInteger('id_voie'); // NOT NULL dans votre SQL

            $table->foreign('id_zone')->references('id_zone')->on('Zone');
            $table->foreign('id_ouvrage_lie')->references('id_ouvrage')->on('ouvrage');
            $table->foreign('id_ouvrage_fin')->references('id_ouvrage')->on('ouvrage');
            $table->foreign('id_ouvrage_debut')->references('id_ouvrage')->on('ouvrage');
            $table->foreign('id_voie')->references('id_voie')->on('voie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('troncon');
    }
};