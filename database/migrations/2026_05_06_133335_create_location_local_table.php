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
        Schema::create('location_local', function (Blueprint $table) {
            $table->unsignedBigInteger('id_local');
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_decision');

            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_utilisation')->nullable();
            $table->text('etat_lieux_entree')->nullable();
            $table->text('etat_lieux_sortie')->nullable();
            $table->decimal('caution_retenue', 10, 2)->nullable();
            $table->date('date_modification')->nullable();
            $table->string('statut_ligne', 50)->nullable();

            $table->primary(['id_local', 'id_contrat', 'id_decision']);
            $table->foreign('id_local')->references('id_local')->on('local_');
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
        Schema::dropIfExists('location_local');
    }
};