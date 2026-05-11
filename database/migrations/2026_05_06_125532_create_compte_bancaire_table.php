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
        Schema::create('compte_bancaire', function (Blueprint $table) {
            $table->id('id_compte');
            $table->string('iban', 34);
            $table->string('rib', 50)->nullable();
            $table->string('bic', 11);
            $table->string('titulaire_compte', 100);
            $table->date('date_ajout')->nullable();

            $table->unsignedBigInteger('id_tiers'); // NOT NULL
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte_bancaire');
    }
};