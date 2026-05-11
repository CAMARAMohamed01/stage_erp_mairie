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
        Schema::create('operation_comptable', function (Blueprint $table) {
            $table->id('id_operation');
            $table->string('numero_operation', 20)->unique();
            $table->string('libelle_operation', 150)->nullable();
            $table->string('nature_operation', 80)->nullable();

            $table->unsignedBigInteger('id_projet')->nullable();
            $table->foreign('id_projet')->references('id_projet')->on('projet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_comptable');
    }
};