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
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('initiales', 5)->nullable();
            $table->string('nom_user', 50);
            $table->string('prenom_user', 50);
            $table->string('role_appli', 50);
            $table->string('emailpro', 80)->nullable();

            $table->unsignedBigInteger('id_service')->nullable();
            $table->foreign('id_service')->references('id_service')->on('service_mairie');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur');
    }
};