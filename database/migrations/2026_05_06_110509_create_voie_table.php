<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voie', function (Blueprint $table) {
            $table->id('id_voie');
            $table->string('ancien_numero', 20)->nullable();
            $table->string('num_provisoire', 20)->nullable();
            $table->integer('longueur_classee_ml')->nullable();
            $table->integer('longueur_reelle_ml')->nullable();
            
            // NUMERIC(6,2) devient decimal('nom', total_chiffres, chiffres_apres_virgule)
            $table->decimal('largeur_moyenne_m', 6, 2)->nullable(); 
            
            $table->string('nom_voie', 100)->nullable();
            $table->string('categorie_voie', 80)->nullable();
            $table->string('numero_voie', 10)->nullable();
            $table->string('point_origine', 50)->nullable();
            $table->string('point_extremite', 100)->nullable();
            $table->text('historique_incorporation')->nullable();
            $table->text('definition_trace')->nullable();
            $table->text('observations_statut')->nullable();
            
            // BOOLEAN
            $table->boolean('est_pdipr')->nullable(); 
            
            $table->string('conformite_cadastrale', 100)->nullable();
            $table->text('interet_touristique')->nullable();
            
            // L'ajout que nous avons décidé juste avant !
            $table->string('statut_juridique', 50)->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voie');
    }
};