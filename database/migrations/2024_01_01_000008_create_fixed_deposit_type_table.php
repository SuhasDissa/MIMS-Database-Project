<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_deposit_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('min_deposit', 15, 2);
            $table->decimal('interest_rate', 5, 4);
            $table->integer('tenure_months');
            $table->string('description', 256)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_deposit_type');
    }
};