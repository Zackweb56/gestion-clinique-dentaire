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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->text('patient_full_name');
            $table->text('email')->nullable();
            $table->text('cin')->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['H', 'F'])->nullable();
            $table->text('phone')->nullable();
            $table->string('address')->nullable();
            $table->enum('insurance_type', ['CNSS', 'CNOPS', 'privé', 'aucun'])->nullable();
            $table->enum('status', ['active', 'new'])->nullable();
            $table->string('created_by'); // stores auth user name
            $table->string('updated_by')->nullable(); // stores auth user name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};