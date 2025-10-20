<?php

use App\Enums\EmployeePosition;
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
        // Update existing position values to match enum values
        DB::table('employee')->update(['position' => DB::raw("LOWER(REPLACE(position, ' ', '_'))")]);

        // Change column to use enum values
        Schema::table('employee', function (Blueprint $table) {
            $table->enum('position', EmployeePosition::values())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->string('position', 50)->change();
        });
    }
};
