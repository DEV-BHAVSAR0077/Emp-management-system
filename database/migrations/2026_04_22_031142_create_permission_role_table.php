<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the permission_role pivot table.
     * Each row links one role to one permission (many-to-many).
     * The UI will present permissions as checkboxes when editing a role.
     */
    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();       // Remove assignments when role is deleted
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();       // Remove assignments when permission is deleted
            $table->unique(['role_id', 'permission_id']); // No duplicate pairs
            $table->timestamps();
        });
    }

    /**
     * Drop the permission_role pivot table.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
