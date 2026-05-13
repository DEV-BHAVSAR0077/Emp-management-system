<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the expenses table.
     * Each expense record is linked to a user, a category, and optionally a sub-category.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Who created this expense
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Category (required)
            $table->foreignId('expense_category_id')
                  ->constrained('expense_categories')
                  ->restrictOnDelete();

            // Sub-category (optional — not every category has sub-categories)
            $table->foreignId('expense_sub_category_id')
                  ->nullable()
                  ->constrained('expense_sub_categories')
                  ->nullOnDelete();

            $table->string('name', 150);                  // Expense title / description
            $table->decimal('amount', 12, 2);             // Supports up to 9,999,999,999.99
            $table->text('note')->nullable();              // Optional detailed note
            $table->date('expense_date');                  // When the expense occurred

            $table->timestamps();
            $table->softDeletes();

            // Index for common queries: filter by user, category, date range
            $table->index(['user_id', 'expense_date']);
            $table->index(['expense_category_id']);
        });
    }

    /**
     * Drop the expenses table.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
