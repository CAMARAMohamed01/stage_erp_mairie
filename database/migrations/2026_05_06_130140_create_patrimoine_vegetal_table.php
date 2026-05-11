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
        Schema::create('patrimoine_vegetal', function (Blueprint $table) {
            $table->id('id_vegetal');
            $table->string('type_vegetal', 50);
            $table->integer('quantite')->nullable();
            $table->string('espece_vegetal', 80)->nullable();
            $table->date('date_plantation')->nullable();

            $table->unsignedBigInteger('id_lieu'); // NOT NULL
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimoine_vegetal');
    }
};