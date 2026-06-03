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
        Schema::create('tiers', function (Blueprint $table) {
            $table->id('id_tiers');
            $table->string('type_tiers', 50); // 'Physique' ou 'Morale'
            $table->string('tel_tiers', 50)->nullable();
            $table->string('email_tiers', 100)->unique()->nullable();

            $table->unsignedBigInteger('id_adresse')->nullable();
            $table->foreign('id_adresse')->references('id_adresse')->on('Adresse');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};