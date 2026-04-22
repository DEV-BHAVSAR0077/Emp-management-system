<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the roles table.
     * Permissions are assigned to roles via the permission_role pivot table.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();        // e.g. "Admin", "HR", "Editor"
            $table->string('description')->nullable();    // Optional human-readable detail
            $table->string('color', 20)->default('#374151'); // Badge color for UI
            $table->boolean('is_protected')->default(false); // Prevent deletion (e.g. Admin)
            $table->timestamps();
        });
    }

    /**
     * Drop the roles table.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
