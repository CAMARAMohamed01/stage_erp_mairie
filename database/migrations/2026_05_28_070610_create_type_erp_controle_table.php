<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('type_erp_controle', function (Blueprint $table) {
            // Déclaration des colonnes (Assurez-vous que le type correspond à vos clés primaires, 
            // 'integer' pour du SERIAL classique, 'unsignedBigInteger' si vous utilisez le standard Laravel récent)
            $table->integer('id_type_erp');
            $table->integer('id_controle');

            // Clé primaire composée (un contrôle ne peut être lié qu'une seule fois au même ERP)
            $table->primary(['id_type_erp', 'id_controle']);

            // Clés étrangères avec suppression en cascade
            // Si on supprime un type ERP ou un Contrôle, la liaison disparaît automatiquement
            $table->foreign('id_type_erp')
                ->references('id_type_erp')->on('type_erp')
                ->onDelete('cascade');

            $table->foreign('id_controle')
                ->references('id_controle')->on('controle_reglementaire')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('type_erp_controle');
    }
};