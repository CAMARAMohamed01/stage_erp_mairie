<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projet', function (Blueprint $table) {
            // On ajoute la colonne. On la met nullable pour ne pas casser les projets existants.
            $table->string('type_projet', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::table('projet', function (Blueprint $table) {
            $table->dropColumn('type_projet');
        });
    }
};