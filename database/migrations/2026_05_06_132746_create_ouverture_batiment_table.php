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
        Schema::create('ouverture_batiment', function (Blueprint $table) {
            $table->unsignedBigInteger('id_batiment');
            $table->unsignedBigInteger('id_support');
            $table->primary(['id_batiment', 'id_support']);
            $table->foreign('id_batiment')->references('id_batiment')->on('batiment');
            $table->foreign('id_support')->references('id_support')->on('support_acces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouverture_batiment');
    }
};