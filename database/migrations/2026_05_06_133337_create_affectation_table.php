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
        Schema::create('affectation', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_support');
            $table->date('date_remise'); // NOT NULL
            $table->date('date_restitution')->nullable();
            $table->boolean('attestation_signee')->nullable();
            $table->string('commentaire', 150)->nullable();

            $table->primary(['id_user', 'id_support']);
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->foreign('id_support')->references('id_support')->on('support_acces');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectation');
    }
};