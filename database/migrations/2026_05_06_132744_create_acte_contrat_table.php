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
        Schema::create('acte_contrat', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_decision');
            $table->primary(['id_contrat', 'id_decision']);
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acte_contrat');
    }
};