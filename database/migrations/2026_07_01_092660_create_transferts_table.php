<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mouvement_id')->unique()->constrained('mouvements')->onDelete('cascade');
            $table->foreignId('stock_source_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('stock_destination_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('commande_id')->nullable()->constrained('commandes')->onDelete('set null');
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'annule'])->default('en_cours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferts');
    }
};
