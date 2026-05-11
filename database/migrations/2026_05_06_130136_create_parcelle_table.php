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
        Schema::create('parcelle', function (Blueprint $table) {
            $table->id('id_parcelle');
            $table->string('num_parcelle', 5);
            $table->string('type_parcelle', 50)->nullable();
            $table->string('section_cadastrale', 1);
            $table->decimal('surface_cadastrale', 8, 2)->nullable();
            $table->text('note_parcelle')->nullable();
            $table->geometry('geom_parcelle')->nullable();

            $table->unsignedBigInteger('id_immo')->nullable();
            $table->unsignedBigInteger('id_lieu_dit'); // NOT NULL

            $table->foreign('id_immo')->references('id_immo')->on('immobilisation_inventaire_');
            $table->foreign('id_lieu_dit')->references('id_lieu_dit')->on('lieu_dit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelle');
    }
};