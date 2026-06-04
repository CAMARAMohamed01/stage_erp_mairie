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
        Schema::create('batiment', function (Blueprint $table) {
            $table->id('id_batiment');
            $table->string('nom_bat', 100);
            $table->decimal('surface_totale_m2', 8, 4)->nullable();
            $table->date('date_construction')->nullable();

            $table->unsignedBigInteger('id_parcelle'); // NOT NULL
            $table->unsignedBigInteger('id_type_erp'); // NOT NULL
            $table->unsignedBigInteger('id_adresse'); // NOT NULL
            $table->unsignedBigInteger('id_immo')->nullable()->unique();

            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_type_erp')->references('id_type_erp')->on('type_erp');
            $table->foreign('id_adresse')->references('id_adresse')->on('Adresse');
            $table->foreign('id_immo')->references('id_immo')->on('immobilisation_inventaire_');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batiment');
    }
};