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
        Schema::create('union_civile', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tiers_id_partenaire1');
            $table->unsignedBigInteger('id_tiers_id_partenaire2');
            $table->string('type_union', 50);
            $table->date('date_union')->nullable();
            $table->string('lieu_union', 50)->nullable();
            $table->date('date_dissolution')->nullable();

            $table->primary(['id_tiers_id_partenaire1', 'id_tiers_id_partenaire2']);
            // Les deux clés tapent sur la même table tiers_physique
            $table->foreign('id_tiers_id_partenaire1')->references('id_tiers')->on('tiers_physique');
            $table->foreign('id_tiers_id_partenaire2')->references('id_tiers')->on('tiers_physique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('union_civile');
    }
};