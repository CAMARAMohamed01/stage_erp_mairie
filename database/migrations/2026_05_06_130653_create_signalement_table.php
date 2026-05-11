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
        Schema::create('signalement', function (Blueprint $table) {
            $table->id('id_sig');
            $table->timestamp('date_creation'); // Format TIMESTAMP
            $table->string('emetteur_nom', 50)->nullable();
            $table->string('emetteur_contact', 12)->nullable();
            $table->string('description', 500);
            $table->string('mode_reception', 100);
            $table->string('priorite', 50);
            $table->string('statut_signalement', 50);

            // L'agent à qui le ticket est assigné (nullable car au début, personne n'est assigné)
            $table->unsignedBigInteger('id_user_assigne')->nullable()->after('id_user');
            $table->unsignedBigInteger('id_user'); // NOT NULL
            $table->unsignedBigInteger('id_adresse')->nullable();
            $table->unsignedBigInteger('id_local')->nullable();
            $table->unsignedBigInteger('id_cat')->nullable();
            $table->unsignedBigInteger('id_tiers')->nullable()->after('statut_signalement');

            $table->foreign('id_user_assigne')->references('id_user')->on('utilisateur');
            $table->foreign('id_tiers')->references('id_tiers')->on('tiers');
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
            $table->foreign('id_adresse')->references('id_adresse')->on('Adresse');
            $table->foreign('id_local')->references('id_local')->on('local_');
            $table->foreign('id_cat')->references('id_cat')->on('categorie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signalement', function (Blueprint $table) {
            $table->dropForeign(['id_tiers']);
            $table->dropColumn('id_tiers');
        });

        Schema::dropIfExists('signalement');
    }
};