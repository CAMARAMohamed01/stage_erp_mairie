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
        Schema::table('document', function (Blueprint $table) {
            $table->renameColumn('id_dossier_1', 'id_dossier_f');
        });
    }

    public function down()
    {
        Schema::table('document', function (Blueprint $table) {
            $table->renameColumn('id_dossier_f', 'id_dossier_1');
        });
    }

};