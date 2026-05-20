<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the payments table.
     * Each payment is linked to a user (who created it), an agency/vendor,
     * and optionally an expense that is being paid.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Who created this payment
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Agency/Vendor receiving the payment (required)
            $table->foreignId('agency_vendor_id')
                  ->constrained('agency_vendors')
                  ->restrictOnDelete();

            // Expense this payment is associated with (optional)
            $table->foreignId('expense_id')
                  ->nullable()
                  ->constrained('expenses')
                  ->nullOnDelete();

            $table->decimal('amount', 12, 2);            // Payment amount
            $table->text('notes')->nullable();            // Optional notes
            $table->date('payment_date');                 // When the payment was made

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['agency_vendor_id']);
            $table->index(['expense_id']);
            $table->index(['user_id', 'payment_date']);
        });
    }

    /**
     * Drop the payments table.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
