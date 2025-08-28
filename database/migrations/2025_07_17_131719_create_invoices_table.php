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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 32)->unique()->notNull();
            $table->foreignId('acte_id')->constrained()->onDelete('cascade');
            $table->decimal('base_amount', 8, 2); // المبلغ قبل الضريبة
            $table->decimal('tva_rate', 5, 2)->nullable(); // نسبة TVA مثلا: 20.00
            $table->decimal('tva_amount', 8, 2)->nullable(); // المبلغ TVA
            $table->decimal('total_amount', 8, 2); // المجموع: base + tva
            $table->decimal('paid_amount', 8, 2)->default(0);
            $table->enum('status', ['impayé','partiel','payé'])->default('impayé');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};