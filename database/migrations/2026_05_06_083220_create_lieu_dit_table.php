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
        Schema::create('lieu_dit', function (Blueprint $table) {

            $table->id('id_lieu_dit');

            // nom_lieu_dit VARCHAR(80) NOT NULL
            $table->string('nom_lieu_dit', 80);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lieu_dit');
    }
};