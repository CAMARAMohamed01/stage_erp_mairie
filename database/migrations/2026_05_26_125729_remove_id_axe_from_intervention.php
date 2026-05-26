<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // On vérifie d'abord si la table et la colonne existent
        if (Schema::hasTable('intervention') && Schema::hasColumn('intervention', 'id_axe')) {
            Schema::table('intervention', function (Blueprint $table) {
                // Suppression de la contrainte de clé étrangère
                // Laravel déduit automatiquement le nom de la contrainte via ['id_axe']
                $table->dropForeign(['id_axe']);

                // Suppression de la colonne
                $table->dropColumn('id_axe');
            });
        }
    }

    public function down()
    {
        // Si tu as besoin de revenir en arrière
        if (Schema::hasTable('intervention') && !Schema::hasColumn('intervention', 'id_axe')) {
            Schema::table('intervention', function (Blueprint $table) {
                $table->integer('id_axe')->nullable();
                $table->foreign('id_axe')->references('id_axe')->on('axe_strategique');
            });
        }
    }
};