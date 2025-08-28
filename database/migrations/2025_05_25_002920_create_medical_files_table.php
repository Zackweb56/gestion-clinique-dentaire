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
        Schema::create('medical_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            $table->text('chronic_diseases')->nullable();
            $table->text('current_medications')->nullable();
            $table->text('allergies')->nullable();
            $table->text('notes')->nullable(); // general or medical notes
            
            $table->string('created_by')->nullable(); // name of the user who created the file
            $table->string('updated_by')->nullable(); // name of the user who updated the file
            $table->string('file_number')->unique()->notNull(); // e.g., DM458-180625
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_files');
    }
};