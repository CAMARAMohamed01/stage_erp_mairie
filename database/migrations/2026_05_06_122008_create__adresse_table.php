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
        Schema::create('Adresse', function (Blueprint $table) {
            $table->id('id_adresse');
            $table->integer('num_rue')->nullable();
            $table->string('rep', 5)->nullable();
            $table->string('nom_voie', 180);
            $table->string('code_postal', 6);
            $table->string('ville', 100);
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->unsignedBigInteger('id_lieu_dit');
            $table->foreign('id_lieu_dit')->references('id_lieu_dit')->on('lieu_dit');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_adresse');
    }
};