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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('consultation_number')->unique()->notNull();
            $table->foreignId('medical_file_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->dateTime('consultation_date');
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('acte_plan')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('responsable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};