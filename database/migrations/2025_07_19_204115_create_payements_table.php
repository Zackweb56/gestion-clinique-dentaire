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
        Schema::create('payements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id'); 
            $table->decimal('amount', 10, 2); 
            $table->enum('payment_method', ['espece', 'carte_bancaire', 'cheque', 'virement'])->nullable(); 
            $table->dateTime('paid_at'); 
            $table->text('notes')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps(); 

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payements');
    }
};