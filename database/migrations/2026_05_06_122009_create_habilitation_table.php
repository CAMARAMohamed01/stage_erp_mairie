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
        Schema::create('habilitation', function (Blueprint $table) {
            // Pas de $table->id() ici, c'est une table pivot pure !
            $table->unsignedBigInteger('id_profil');
            $table->unsignedBigInteger('id_module');

            $table->boolean('droit_lecture');
            $table->boolean('droit_ecriture');
            $table->boolean('droit_suppression');
            $table->integer('niveau_validation')->nullable();
            $table->boolean('droit_validation');

            // On déclare la clé primaire composite
            $table->primary(['id_profil', 'id_module']);

            // Les contraintes
            $table->foreign('id_profil')->references('id_profil')->on('profil_acces');
            $table->foreign('id_module')->references('id_module')->on('module_logiciel');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habilitation');
    }
};