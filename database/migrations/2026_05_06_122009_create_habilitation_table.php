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
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_module');

            $table->boolean('droit_lecture');
            $table->boolean('droit_ecriture');
            $table->boolean('droit_suppression');
            $table->integer('niveau_validation')->nullable();
            $table->boolean('droit_validation');

            // On déclare la clé primaire composite
            $table->primary(['id_user', 'id_module']);

            // Les contraintes
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
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