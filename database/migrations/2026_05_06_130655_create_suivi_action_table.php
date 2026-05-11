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
        Schema::create('suivi_action', function (Blueprint $table) {
            $table->id('id_action');
            $table->date('date_action_suivi');
            $table->decimal('cout_associe', 10, 2)->nullable();
            $table->decimal('temps_passe_heures', 4, 2)->nullable();
            $table->string('statut_apres_action', 50);
            $table->text('description_etape');

            $table->unsignedBigInteger('id_int');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivi_action');
    }
};