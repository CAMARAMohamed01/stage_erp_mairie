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
        Schema::create('article_chapitre', function (Blueprint $table) {
            $table->unsignedBigInteger('id_chapitre');
            $table->unsignedBigInteger('id_article');

            $table->primary(['id_chapitre', 'id_article']);

            $table->foreign('id_chapitre')->references('id_chapitre')->on('chapitre');
            $table->foreign('id_article')->references('id_article')->on('article_compta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_chapitre');
    }
};