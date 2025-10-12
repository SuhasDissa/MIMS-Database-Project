<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 10);
            $table->string('branch_name', 50);
            $table->string('address', 100);
            $table->string('city', 50);
            $table->string('postal_code', 20);
            $table->string('phone', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch');
    }
};