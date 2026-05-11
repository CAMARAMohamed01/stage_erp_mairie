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
        Schema::create('projet_lieu', function (Blueprint $table) {
            $table->unsignedBigInteger('id_projet');
            $table->unsignedBigInteger('id_lieu');
            $table->primary(['id_projet', 'id_lieu']);
            $table->foreign('id_projet')->references('id_projet')->on('projet')->onDelete('cascade');
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_lieu');
    }
};