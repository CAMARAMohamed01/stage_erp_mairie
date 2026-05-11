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
        Schema::create('gestionnaire_parcelle', function (Blueprint $table) {
            $table->unsignedBigInteger('id_parcelle');
            $table->unsignedBigInteger('id_tiers');
            $table->string('type_gestion', 100)->nullable();
            $table->date('date_debut_gestion')->nullable();
            $table->date('date_fin_gestion')->nullable();

            $table->primary(['id_parcelle', 'id_tiers']);
            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestionnaire_parcelle');
    }
};