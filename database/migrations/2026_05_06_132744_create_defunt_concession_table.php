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
        Schema::create('defunt_concession', function (Blueprint $table) {
            $table->unsignedBigInteger('id_concession');
            $table->unsignedBigInteger('id_tiers');
            $table->primary(['id_concession', 'id_tiers']);
            $table->foreign('id_concession')->references('id_concession')->on('concession_cimetiere');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers_physique'); // Spécifique au SQL !
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defunt_concession');
    }
};