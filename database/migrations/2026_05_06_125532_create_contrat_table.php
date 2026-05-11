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
        Schema::create('contrat', function (Blueprint $table) {
            $table->id('id_contrat');
            $table->string('numero_contrat', 30)->unique()->nullable();
            $table->string('type_contrat', 50);
            $table->string('objet_contrat', 255)->nullable();
            $table->boolean('revision_prix_prevue')->nullable();
            $table->date('date_signature_contrat')->nullable();
            $table->date('date_debut_contrat');
            $table->date('date_fin_contrat')->nullable();
            $table->decimal('prix_mois', 10, 2)->nullable();
            $table->decimal('prix_annuel', 10, 2)->nullable();
            $table->integer('duree_mois')->nullable();
            $table->string('modalite_renouvellement', 255)->nullable();
            $table->integer('preavis_resiliation_mois')->nullable();
            $table->string('code_imputation', 20)->nullable();
            $table->string('lot', 20)->nullable();
            $table->string('code_analytique', 100)->nullable();
            $table->string('frequence_facturation', 100)->nullable();
            $table->string('mode_reglement', 50)->nullable();
            $table->date('date_echeance')->nullable();

            $table->unsignedBigInteger('id_tiers'); // NOT NULL
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrat');
    }
};