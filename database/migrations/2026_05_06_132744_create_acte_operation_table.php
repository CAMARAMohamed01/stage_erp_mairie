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
        Schema::create('acte_operation', function (Blueprint $table) {
            $table->unsignedBigInteger('id_decision');
            $table->unsignedBigInteger('id_operation');
            $table->primary(['id_decision', 'id_operation']);
            $table->foreign('id_decision')->references('id_decision')->on('decision_administratif');
            $table->foreign('id_operation')->references('id_operation')->on('operation_comptable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acte_operation');
    }
};