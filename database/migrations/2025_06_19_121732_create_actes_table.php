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
        Schema::create('actes', function (Blueprint $table) {
            $table->id();
            $table->string('acte_number')->unique()->notNull();
            $table->foreignId('medical_file_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->date('acte_date');
            $table->enum('payment_status', ['non payé', 'partiel', 'payé'])->default('non payé');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actes');
    }
};