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
        Schema::create('article_budget', function (Blueprint $table) {
            $table->unsignedBigInteger('id_article');
            $table->unsignedBigInteger('id_budget');
            $table->primary(['id_article', 'id_budget']);
            $table->foreign('id_article')->references('id_article')->on('article_compta');
            $table->foreign('id_budget')->references('id_budget')->on('enveloppe_budgetaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_budget');
    }
};