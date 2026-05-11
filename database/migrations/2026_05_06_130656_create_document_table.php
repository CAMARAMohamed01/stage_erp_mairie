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
        Schema::create('document', function (Blueprint $table) {
            $table->id('id_document');
            $table->string('nom_fichier', 80)->nullable();
            $table->string('type_doc', 50)->nullable();
            $table->string('chemin_stockage', 500)->nullable();
            $table->decimal('taille_ko', 10, 2)->nullable();
            $table->date('date_upload')->nullable();

            $table->unsignedBigInteger('id_decision')->nullable();
            $table->unsignedBigInteger('id_compteur')->nullable();
            $table->unsignedBigInteger('id_local')->nullable();
            $table->unsignedBigInteger('id_local_1')->nullable();
            $table->unsignedBigInteger('id_parcelle')->nullable();
            $table->unsignedBigInteger('id_lieu')->nullable();
            $table->unsignedBigInteger('id_equipement')->nullable();
            $table->unsignedBigInteger('id_int')->nullable();
            $table->unsignedBigInteger('id_tiers')->nullable();
            $table->unsignedBigInteger('id_dossier')->nullable();
            $table->unsignedBigInteger('id_contrat')->nullable();
            $table->unsignedBigInteger('id_dossier_1')->nullable(); // Dossier Financier

            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
            $table->foreign('id_compteur')->references('id_compteur')->on('compteur');
            $table->foreign('id_local')->references('id_local')->on('local_');
            $table->foreign('id_local_1')->references('id_local')->on('local_');
            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement');
            $table->foreign('id_int')->references('id_int')->on('intervention');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_dossier')->references('id_dossier')->on('dossier_urba');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_dossier_1')->references('id_dossier')->on('dossier_financier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};