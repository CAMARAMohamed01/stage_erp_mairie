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
        Schema::create('emplacement_funeraire', function (Blueprint $table) {
            $table->id('id_emplacement');
            $table->string('reference_emplacement', 50)->nullable();
            $table->string('type_emplacement', 50)->nullable();
            $table->integer('capacite_max')->nullable();
            $table->string('statut_occupation', 50)->nullable();

            $table->unsignedBigInteger('id_lieu')->nullable();
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplacement_funeraire');
    }
};