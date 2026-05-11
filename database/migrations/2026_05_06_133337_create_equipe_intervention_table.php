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
        Schema::create('equipe_intervention', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_int');
            $table->string('role_agent', 50)->nullable();
            $table->decimal('nb_heures_passees', 4, 2)->nullable();

            $table->primary(['id_user', 'id_int']);
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipe_intervention');
    }
};