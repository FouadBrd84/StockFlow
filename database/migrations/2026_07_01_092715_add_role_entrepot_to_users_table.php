<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'gestionnaire', 'fournisseur'])->default('gestionnaire');
            $table->foreignId('entrepot_id')->nullable()->unique()->constrained('entrepots')->onDelete('set null');
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('entrepot_id');
            $table->dropColumn('telephone');
            $table->dropColumn('adresse');
        });
    }
};
