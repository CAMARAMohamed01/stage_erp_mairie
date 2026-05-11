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
        Schema::create('ouverture_equipement', function (Blueprint $table) {
            $table->unsignedBigInteger('id_equipement');
            $table->unsignedBigInteger('id_support');
            $table->primary(['id_equipement', 'id_support']);
            $table->foreign('id_equipement')->references('id_equipement')->on('equipement');
            $table->foreign('id_support')->references('id_support')->on('support_acces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouverture_equipement');
    }
};