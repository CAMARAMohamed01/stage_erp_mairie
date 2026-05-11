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
        Schema::create('ouverture_lieu', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lieu');
            $table->unsignedBigInteger('id_support');
            $table->primary(['id_lieu', 'id_support']);
            $table->foreign('id_lieu')->references('id_lieu')->on('lieux_publics');
            $table->foreign('id_support')->references('id_support')->on('support_acces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouverture_lieu');
    }
};