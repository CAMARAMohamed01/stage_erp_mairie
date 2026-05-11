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
        Schema::create('usage_contrat', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usage');
            $table->unsignedBigInteger('id_contrat');
            $table->primary(['id_usage', 'id_contrat']);
            $table->foreign('id_usage')->references('id_usage')->on('type_usage');
            $table->foreign('id_contrat')->references('id_contrat')->on('contrat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_contrat');
    }
};