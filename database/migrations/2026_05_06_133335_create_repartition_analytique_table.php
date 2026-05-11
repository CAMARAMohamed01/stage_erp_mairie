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
        Schema::create('repartition_analytique', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ligne');
            $table->unsignedBigInteger('id_code');
            $table->decimal('pourcentage_repartition', 5, 2)->nullable();

            $table->primary(['id_ligne', 'id_code']);
            $table->foreign('id_ligne')->references('id_ligne')->on('ligne_financiere_facture_');
            $table->foreign('id_code')->references('id_code')->on('code_analytique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartition_analytique');
    }
};