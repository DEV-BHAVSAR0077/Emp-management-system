<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the expense_categories table.
     * Top-level grouping for expenses (e.g. Travel, Office Supplies, Meals).
     */
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Drop the expense_categories table.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
