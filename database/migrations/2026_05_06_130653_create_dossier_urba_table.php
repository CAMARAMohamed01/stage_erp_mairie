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
        Schema::create('dossier_urba', function (Blueprint $table) {
            $table->id('id_dossier');
            $table->string('numero_dossier', 50);
            $table->string('type_dossier_CU_DP_', 10)->nullable();
            $table->date('date_depot')->nullable();
            $table->date('date_decision')->nullable();
            $table->string('nature_decision', 50)->nullable();
            $table->text('objet_travaux')->nullable();
            $table->decimal('surface_plancher_m2', 10, 2)->nullable();
            $table->decimal('hauteur_construction', 5, 2)->nullable();
            $table->decimal('prix_m2_ia', 10, 2)->nullable();
            $table->date('date_limite_instruction')->nullable();
            $table->text('avis_maire')->nullable();
            $table->text('observations')->nullable();

            $table->unsignedBigInteger('id_tiers')->nullable();
            $table->unsignedBigInteger('id_acte_decision')->nullable();
            $table->unsignedBigInteger('id_user_instructeur')->nullable();

            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_acte_decision')->references('id_decision')->on('decision_administratif');
            $table->foreign('id_user_instructeur')->references('id_user')->on('utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_urba');
    }
};