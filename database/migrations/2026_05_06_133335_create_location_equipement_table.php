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
        Schema::create('location_equipement', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_equipement');
            $table->unsignedBigInteger('id_decision');

            $table->integer('quantite_louee'); // NOT NULL
            $table->integer('quantite_rendue')->nullable();
            $table->string('etat_depart', 100)->nullable();
            $table->string('etat_retour', 100)->nullable();
            $table->decimal('montant_penalite', 10, 2)->nullable();
            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_utilisation')->nullable();
            $table->date('date_modification')->nullable();
            $table->string('statut_ligne', 50)->nullable();

            $table->primary(['id_contrat', 'id_equipement', 'id_decision']);
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement');
            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_equipement');
    }
};