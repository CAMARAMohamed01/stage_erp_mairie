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
        Schema::create('plan_entretien_vert', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lieu');
            $table->unsignedBigInteger('id_tache_verte');
            $table->text('observations')->nullable();

            $table->primary(['id_lieu', 'id_tache_verte']);
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_tache_verte')->references('id_tache_verte')->on('type_tache_verte');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_entretien_vert');
    }
};