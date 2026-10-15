<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('entrepot_id')->constrained('entrepots')->onDelete('cascade');
            $table->enum('statut', ['en_attente', 'confirmee', 'expediee', 'recue', 'annulee'])->default('en_attente');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
