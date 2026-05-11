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
        Schema::create('decision_commission', function (Blueprint $table) {
            $table->id('id_decision');
            $table->date('date_commission');
            $table->string('statut_decision', 50)->nullable();
            $table->text('commentaire_elus')->nullable();

            $table->unsignedBigInteger('id_enregistreur_decision')->nullable();
            $table->unsignedBigInteger('id_int')->nullable();
            $table->unsignedBigInteger('id_operation')->nullable();

            $table->foreign('id_enregistreur_decision')->references('id_user')->on('utilisateur');
            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_commission');
    }
};