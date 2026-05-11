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
        Schema::create('releve_compteur', function (Blueprint $table) {
            $table->id('id_releve');
            $table->date('date_releve');
            $table->decimal('valeur_index', 10, 2)->nullable();
            $table->string('commentaire_releve', 150)->nullable();

            $table->unsignedBigInteger('id_compteur');
            $table->foreign('id_compteur')->references('id_compteur')->on('compteur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releve_compteur');
    }
};