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
        Schema::create('service_mairie', function (Blueprint $table) {
        $table->id('id_service');
        $table->string('nom_service', 100);
        
        // La clé étrangère vers sa propre table
        $table->unsignedBigInteger('id_service_parent')->nullable();
        $table->foreign('id_service_parent')->references('id_service')->on('service_mairie');
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_mairie');
    }
};
