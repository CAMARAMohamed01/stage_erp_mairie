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
        Schema::create('ouvrage', function (Blueprint $table) {
            $table->id('id_ouvrage');
            $table->string('nom_ouvrage', 100);
            $table->string('type_ouvrage', 50)->nullable();
            $table->string('domaine', 50)->nullable();
            $table->string('voie_portee', 100)->nullable();
            $table->string('franchissement', 50)->nullable();
            $table->boolean('sous_loi_didier')->nullable();
            $table->boolean('est_programme_national')->nullable();
            $table->boolean('dimension_sup_2m')->nullable();
            $table->string('classe_longueur_mur', 50)->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Le fameux type spatial
            $table->geometry('trace_geometrique')->nullable();

            $table->date('date_transmission_etat')->nullable();
            $table->text('commentaire')->nullable();

            $table->unsignedBigInteger('id_voie')->nullable();
            $table->foreign('id_voie')->references('id_voie')->on('voie');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouvrage');
    }
};