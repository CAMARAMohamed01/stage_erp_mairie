<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_acces', function (Blueprint $table) {
        $table->id('id_support');
        $table->string('numero_serie', 50);
        $table->string('type_support', 50)->nullable();
        $table->boolean('est_actif')->default(true)->nullable(); // Le default est une bonne pratique
        $table->string('observations', 250)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_acces');
    }
};
