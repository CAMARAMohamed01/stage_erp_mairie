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
        Schema::create('type_erp', function (Blueprint $table) {
            $table->id('id_type_erp');
            $table->string('reglementation_applicable', 80);
            $table->string('public_cible', 80)->nullable();
            $table->integer('categorie_erp')->nullable();
            $table->string('type_erp', 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_erp');
    }
};