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
        Schema::create('achat_materiel_consommable', function (Blueprint $table) {
            $table->id('id_achat');
            $table->string('nom_materiel', 150);
            $table->decimal('quantite', 10, 2)->nullable();
            $table->string('unite_mesure', 50)->nullable();
            $table->decimal('prix_unitaire_ht', 10, 2)->nullable();
            $table->date('date_achat');
            $table->string('statut_achat', 50)->nullable();

            $table->unsignedBigInteger('id_int')->nullable();
            $table->unsignedBigInteger('id_operation')->nullable();
            $table->unsignedBigInteger('id_contrat')->nullable();

            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_materiel_consommable');
    }
};