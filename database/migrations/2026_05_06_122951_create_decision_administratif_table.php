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
        Schema::create('decision_administratif', function (Blueprint $table) {
            $table->id('id_decision');
            $table->string('numero_decision', 50);
            $table->date('date_decision');
            $table->text('objet_decision')->nullable();
            $table->text('intitule_decision')->nullable();
            $table->boolean('teletransmission_prefecture')->nullable();
            $table->string('type_decision', 80)->nullable();

            $table->unsignedBigInteger('id_user_redacteur')->nullable();
            $table->foreign('id_user_redacteur')->references('id_user')->on('utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_administratif');
    }
};