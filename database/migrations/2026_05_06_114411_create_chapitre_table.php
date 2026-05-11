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
        Schema::create('chapitre', function (Blueprint $table) {
            $table->id('id_chapitre');
            $table->string('numero_chapitre', 50);
            $table->string('libelle_chapitre', 150)->nullable();
            $table->string('sens_financier', 50)->nullable();
            $table->string('section_budgetaire', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapitre');
    }
};