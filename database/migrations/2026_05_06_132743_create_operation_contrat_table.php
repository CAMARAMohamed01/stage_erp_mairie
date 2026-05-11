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
        Schema::create('operation_contrat', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contrat');
            $table->unsignedBigInteger('id_operation');
            $table->primary(['id_contrat', 'id_operation']);
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_contrat');
    }
};