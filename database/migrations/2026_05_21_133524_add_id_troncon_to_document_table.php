<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('document', function (Blueprint $table) {
            if (!Schema::hasColumn('document', 'id_troncon')) {
                $table->foreignId('id_troncon')->nullable()->constrained('troncon', 'id_troncon');
            }
        });
    }

    public function down()
    {
        Schema::table('document', function (Blueprint $table) {
            if (Schema::hasColumn('document', 'id_troncon')) {
                $table->dropForeign(['id_troncon']);
                $table->dropColumn('id_troncon');
            }
        });
    }
};