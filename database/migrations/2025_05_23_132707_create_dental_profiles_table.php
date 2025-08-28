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
        Schema::create('dental_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('ICE');
            $table->string('IF');
            $table->string('TVA')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('phone_01');
            $table->string('phone_02')->nullable();
            $table->string('email');
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_profiles');
    }
};