<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the permissions table.
     * Each row is one named permission that can be assigned to any role.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();       // e.g. "manage_users"
            $table->string('display_name')->nullable();  // e.g. "Manage Users" (shown in UI checkboxes)
            $table->string('description')->nullable();   // e.g. "Can create, edit and delete users"
            $table->string('group')->nullable();         // e.g. "Users", "Reports" — for grouping checkboxes
            $table->timestamps();
        });
    }

    /**
     * Drop the permissions table.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
