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
        Schema::create('lien_filiation', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tiers_id_enfant');
            $table->unsignedBigInteger('id_tiers_id_parent');
            $table->string('type_filiation', 50)->nullable();

            $table->primary(['id_tiers_id_enfant', 'id_tiers_id_parent']);
            $table->foreign('id_tiers_id_enfant')->references('id_tiers')->on('tiers_physique');
            $table->foreign('id_tiers_id_parent')->references('id_tiers')->on('tiers_physique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lien_filiation');
    }
};