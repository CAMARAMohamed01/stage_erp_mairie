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
        Schema::create('ligne_financiere_facture_', function (Blueprint $table) {
            $table->id('id_ligne');
            $table->date('date_comptable');
            $table->string('designation_ligne', 255);
            $table->decimal('montant_ht', 12, 2)->nullable();
            $table->decimal('montant_tva', 12, 2)->nullable();
            $table->decimal('montant_ttc', 12, 2)->nullable();
            $table->string('nature_charge', 30)->nullable();
            $table->string('periodicite', 50)->nullable();

            $table->unsignedBigInteger('id_operation')->nullable();
            $table->unsignedBigInteger('id_dossier'); // NOT NULL
            $table->unsignedBigInteger('id_budget'); // NOT NULL

            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
            $table->foreign('id_dossier')->references('id_dossier')->on('dossier_financier');
            $table->foreign('id_budget')->references('id_budget')->on('enveloppe_budgetaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_financiere_facture');
    }
};