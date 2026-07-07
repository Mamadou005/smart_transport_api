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
        Schema::create('voyages', function (Blueprint $table) {
    $table->id();
    $table->string('origine');
    $table->string('destination');
    $table->dateTime('date_depart');
    $table->dateTime('date_arrivee');
    $table->string('type_transport');
    $table->string('statut')->default('planifie');
    $table->integer('capacite');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
