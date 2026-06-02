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
        Schema::create('projet', function (Blueprint $table) {
            $table->id('id_projet');
            $table->string('nom_projet', 80);
            $table->decimal('budget_global_alloue', 10, 2)->nullable();
            $table->string('annee_mandat', 5);
            $table->string('avis', 100)->nullable();

            $table->unsignedBigInteger('id_user')->nullable();

            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet');
    }
};