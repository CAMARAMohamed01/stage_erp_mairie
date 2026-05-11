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
        Schema::create('controle_reglementaire', function (Blueprint $table) {
            $table->id('id_controle');
            $table->string('designation', 160);
            $table->string('domaine_technique', 100)->nullable();
            $table->boolean('est_legalement_obligatoire')->nullable();
            $table->smallInteger('frequence_mois')->nullable();
            $table->string('type_controle', 80)->nullable();
            $table->string('type_document_attendu', 100)->nullable();
            $table->string('intervenant_prevu', 100)->nullable();

            $table->unsignedBigInteger('id_lieu')->nullable();
            $table->unsignedBigInteger('id_type_erp')->nullable();

            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_type_erp')->references('id_type_erp')->on('type_erp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controle_reglementaire');
    }
};