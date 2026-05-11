<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On précise le nom exact de la table souhaitée
        Schema::create('lieu_dit', function (Blueprint $table) {
            // id_lieu_dit SERIAL PRIMARY KEY
            $table->id('id_lieu_dit'); 
            
            // nom_lieu_dit VARCHAR(80) NOT NULL
            $table->string('nom_lieu_dit', 80); 
            
            // Note : Laravel ajoute souvent des colonnes de dates automatiques (created_at, updated_at). 
            // Si vous n'en voulez pas pour coller à 100% à votre MCD, vous pouvez commenter la ligne ci-dessous :
            // $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lieu_dit');
    }
};
