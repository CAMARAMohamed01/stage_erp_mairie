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
        Schema::create('Zone', function (Blueprint $table) {
            $table->id('id_zone');
            $table->string('nom_zone', 80);
            $table->string('code_zone', 10)->nullable()->after('id_zone');//
            $table->geometry('geom_zone')->nullable();
            // La clé étrangère
            $table->unsignedBigInteger('id_secteur');
            $table->foreign('id_secteur')->references('id_secteur')->on('secteur');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_zone');
    }
};