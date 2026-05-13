<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the expense_sub_categories table.
     * Each sub-category belongs to one parent expense_category.
     * E.g. category "Travel" → sub-categories "Fuel", "Flights", "Hotels".
     */
    public function up(): void
    {
        Schema::create('expense_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')
                  ->constrained('expense_categories')
                  ->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // A sub-category name must be unique within its parent category
            $table->unique(['expense_category_id', 'name']);
        });
    }

    /**
     * Drop the expense_sub_categories table.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_sub_categories');
    }
};
