<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agency_vendors', function (Blueprint $table) {
            DB::table('agency_vendors')->where('type', 'Agency')->update(['type' => 0]);
            DB::table('agency_vendors')->where('type', 'Vendor')->update(['type' => 1]);

            $table->smallInteger('type')->default(0)->comment('0 = Agency, 1 = Vendor')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agency_vendors', function (Blueprint $table) {
            $table->string('type')->change();
            
            DB::table('agency_vendors')->where('type', 0)->update(['type' => 'Agency']);
            DB::table('agency_vendors')->where('type', 1)->update(['type' => 'Vendor']);
        });
    }
};
