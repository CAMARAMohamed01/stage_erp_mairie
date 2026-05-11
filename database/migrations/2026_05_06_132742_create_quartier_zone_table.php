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
        Schema::create('quartier_zone', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lieu_dit');
            $table->unsignedBigInteger('id_zone');
            $table->primary(['id_lieu_dit', 'id_zone']);
            $table->foreign('id_lieu_dit')->references('id_lieu_dit')->on('lieu_dit');
            $table->foreign('id_zone')->references('id_zone')->on('Zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quartier_zone');
    }
};