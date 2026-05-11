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
        Schema::create('intervention', function (Blueprint $table) {
            $table->id('id_int');
            $table->string('code_budget', 2)->nullable();
            $table->date('date_cloture')->nullable();
            $table->date('date_ouverture');
            $table->string('type_intervention', 150);
            $table->string('statut_global', 50);
            $table->text('description');
            $table->string('Autre', 100)->nullable();

            // Ses (très) nombreuses liaisons !
            $table->unsignedBigInteger('id_adresse')->nullable();
            $table->unsignedBigInteger('id_compteur')->nullable();
            $table->unsignedBigInteger('id_troncon')->nullable();
            $table->unsignedBigInteger('id_axe')->nullable();
            $table->unsignedBigInteger('id_controle')->nullable();
            $table->unsignedBigInteger('id_tiers')->nullable();
            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_cat'); // NOT NULL
            $table->unsignedBigInteger('id_local')->nullable();
            $table->unsignedBigInteger('id_user_demandeur')->nullable();
            $table->unsignedBigInteger('id_service')->nullable();
            $table->unsignedBigInteger('id_sig')->nullable();
            $table->unsignedBigInteger('id_operation')->nullable();

            $table->foreign('id_adresse')->references('id_adresse')->on('Adresse');
            $table->foreign('id_compteur')->references('id_compteur')->on('compteur');
            $table->foreign('id_troncon')->references('id_troncon')->on('troncon');
            $table->foreign('id_axe')->references('id_axe')->on('axe_strategique');
            $table->foreign('id_controle')->references('id_controle')->on('controle_reglementaire');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_cat')->references('id_cat')->on('categorie');
            $table->foreign('id_local')->references('id_local')->on('local_');
            $table->foreign('id_user_demandeur')->references('id_user')->on('utilisateur');
            $table->foreign('id_service')->references('id_service')->on('service_mairie');
            $table->foreign('id_sig')->references('id_sig')->on('signalement');
            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention');
    }
};