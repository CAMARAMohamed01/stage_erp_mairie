<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('intervention', function (Blueprint $table) {
            // Ajout de la colonne id_projet
            $table->integer('id_projet')->nullable();

            // Création de la clé étrangère
            $table->foreign('id_projet')->references('id_projet')->on('projet')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('intervention', function (Blueprint $table) {
            $table->dropForeign(['id_projet']);
            $table->dropColumn('id_projet');
        });
    }
};