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
        Schema::create('historique_signalement', function (Blueprint $table) {
            $table->id('id_hist_sig');
            $table->unsignedBigInteger('id_sig');
            $table->unsignedBigInteger('id_user'); // L'agent qui a fait l'action
            $table->string('statut_avant', 50)->nullable();
            $table->string('statut_apres', 50);
            $table->text('commentaire_interne')->nullable();
            $table->timestamp('date_action')->useCurrent();

            $table->foreign('id_sig')->references('id_sig')->on('signalement')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('utilisateur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_signalement');
    }
};