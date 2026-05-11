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
        Schema::create('voie_zone', function (Blueprint $table) {
            $table->unsignedBigInteger('id_zone');
            $table->unsignedBigInteger('id_voie');
            $table->primary(['id_zone', 'id_voie']);
            $table->foreign('id_zone')->references('id_zone')->on('Zone');
            $table->foreign('id_voie')->references('id_voie')->on('voie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voie_zone');
    }
};