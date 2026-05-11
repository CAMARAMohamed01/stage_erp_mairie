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
        Schema::create('secteur', function (Blueprint $table) {
            $table->id('id_secteur');
            $table->string('nom_secteur', 80);
            $table->string('code_secteur', 10)->nullable()->after('id_secteur');
            $table->geometry('geom_secteur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secteur');
    }
};