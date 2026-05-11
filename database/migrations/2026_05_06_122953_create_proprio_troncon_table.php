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
        Schema::create('proprio_troncon', function (Blueprint $table) {
            // Les 3 colonnes de la clé primaire
            $table->unsignedBigInteger('id_decision');
            $table->unsignedBigInteger('id_troncon');
            $table->unsignedBigInteger('id_tiers_proprietaire');

            $table->date('date_acquisition')->nullable();
            $table->date('date_vente')->nullable();
            $table->decimal('prix_cession', 15, 2)->nullable(); // Mis à 15,2 comme dans votre SQL

            // Déclaration de la clé primaire composite EXACTE
            $table->primary(['id_decision', 'id_troncon', 'id_tiers_proprietaire']);

            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
            $table->foreign('id_troncon')->references('id_troncon')->on('troncon');
            $table->foreign('id_tiers_proprietaire')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proprio_troncon');
    }
};