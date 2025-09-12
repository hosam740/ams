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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Link the payment to the contract
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');

            // Payment number in the contract sequence (1, 2, 3, ...)
            $table->integer('payment_number');

            // Payment due date
            $table->date('due_date');

            // Payment amount
            $table->decimal('amount', 20, 2);

            // Payment status
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');

            // Actual payment date (if paid)
            $table->date('paid_date')->nullable();

            // Actual paid amount (can differ from the required amount)
            $table->decimal('paid_amount', 20, 2)->nullable();

            // Notes on the payment
            $table->text('notes')->nullable();

            $table->timestamps();

            // Unique index to ensure no duplicate payment numbers in the same contract
            $table->unique(['contract_id', 'payment_number'], 'unique_payment_number_per_contract');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
