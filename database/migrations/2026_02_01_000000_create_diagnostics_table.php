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
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('plante'); // Tomate, Poivron, Pomme de terre, Maïs
            $table->string('maladie'); // Mildou, Alternaria, etc.
            $table->string('etat'); // Sain, Malade
            $table->decimal('confiance', 5, 2); // Confiance en %
            $table->string('niveau_risque'); // Faible, Moyen, Élevé
            $table->json('conseils'); // Liste des conseils
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};
