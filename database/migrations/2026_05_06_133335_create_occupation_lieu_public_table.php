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
        Schema::create('occupation_lieu_public', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lieu');
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_decision');

            $table->date('date_debut_occupation')->nullable();
            $table->date('date_fin_occupation')->nullable();
            $table->decimal('surface_occupee_m2', 10, 2)->nullable();
            $table->string('usage_specifique', 255)->nullable();
            $table->text('etat_lieux_avant')->nullable();
            $table->text('etat_lieux_apres')->nullable();
            $table->date('date_modification')->nullable();
            $table->string('statut_ligne', 50)->nullable();

            $table->primary(['id_lieu', 'id_contrat', 'id_decision']);
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupation_lieu_public');
    }
};