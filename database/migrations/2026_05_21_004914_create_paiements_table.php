<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->string('devise', 10)->default('XOF');
            $table->enum('methode', ['wave', 'orange_money', 'cash']);
            $table->enum('statut', ['en_attente', 'confirme', 'echoue', 'rembourse'])
                ->default('en_attente');
            $table->string('reference')->unique()->nullable();
            $table->string('telephone_paiement')->nullable();
            $table->timestamp('confirme_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
