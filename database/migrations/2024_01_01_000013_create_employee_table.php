<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100);
            $table->string('phone', 15);
            $table->string('position', 50);
            $table->string('nic_num', 12);
            $table->integer('branch_id');
            $table->boolean('is_active')->default(true);
            
            $table->foreign('branch_id')->references('id')->on('branch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};