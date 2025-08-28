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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_file_id')->constrained('medical_files')->onDelete('cascade');
            $table->enum('type', ['consultation', 'suivi', 'acte']);
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'done', 'canceled', 'no_show']);
            $table->dateTime('appointment_date');
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};