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
        Schema::create('tiers_physique', function (Blueprint $table) {
            // Pas de $table->id() ! La clé vient du parent.
            $table->unsignedBigInteger('id_tiers')->primary();

            $table->string('civilite', 50)->nullable();
            $table->string('nom_tiers', 50);
            $table->string('prenom_tiers', 50);
            $table->date('date_naissance')->nullable();

            // On relie au Tiers parent (avec suppression en cascade)
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers_physique');
    }
};