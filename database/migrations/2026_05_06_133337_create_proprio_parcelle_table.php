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
        Schema::create('proprio_parcelle', function (Blueprint $table) {
            $table->unsignedBigInteger('id_parcelle');
            $table->unsignedBigInteger('id_tiers');
            $table->date('date_acquisition')->nullable();
            $table->date('date_vente')->nullable();
            $table->decimal('pourcentage_part', 5, 2)->nullable();
            $table->decimal('prix_parcelle', 15, 2)->nullable();

            $table->primary(['id_parcelle', 'id_tiers']);
            $table->foreign('id_parcelle')->references('id_parcelle')->on('parcelle');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proprio_parcelle');
    }
};