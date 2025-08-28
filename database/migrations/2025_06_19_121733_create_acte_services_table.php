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
        Schema::create('acte_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acte_id')->constrained('actes')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->string('tooth_number')->nullable();
            $table->string('libelle')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acte_services');
    }
}; 