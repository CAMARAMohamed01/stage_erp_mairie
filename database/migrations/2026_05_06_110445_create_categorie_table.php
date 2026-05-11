<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie', function (Blueprint $table) {
            $table->id('id_cat'); // Crée un SERIAL PRIMARY KEY
            $table->string('libelle', 80); // VARCHAR(80) NOT NULL
           // $table->timestamps(); // (Optionnel) Ajoute created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorie');
    }
};