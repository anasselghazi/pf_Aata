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
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->decimal('objectif_financier', 15, 2);
            $table->decimal('montant_collecte', 15, 2)->default(0);
            $table->enum('statut', ['en_attente', 'approuvee', 'rejetee', 'terminee'])->default('en_attente');
            $table->foreignId('beneficiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};
