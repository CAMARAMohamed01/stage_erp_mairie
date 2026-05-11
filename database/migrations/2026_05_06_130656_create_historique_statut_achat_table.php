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
        Schema::create('historique_statut_achat', function (Blueprint $table) {
            $table->id('id_historique_achat');
            $table->date('date_action');
            $table->string('statut_applique', 50)->nullable();

            $table->unsignedBigInteger('id_achat');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_achat')->references('id_achat')->on('achat_materiel_consommable');
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_statut_achat');
    }
};