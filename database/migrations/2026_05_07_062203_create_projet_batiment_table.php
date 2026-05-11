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
        Schema::create('projet_batiment', function (Blueprint $table) {
            $table->unsignedBigInteger('id_projet');
            $table->unsignedBigInteger('id_batiment');
            $table->primary(['id_projet', 'id_batiment']);
            $table->foreign('id_projet')->references('id_projet')->on('projet')->onDelete('cascade');
            $table->foreign('id_batiment')->references('id_batiment')->on('batiment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_batiment');
    }
};