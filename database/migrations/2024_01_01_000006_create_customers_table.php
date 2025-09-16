<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->dateTime('date_of_birth');
            $table->enum('gender', ['M', 'F', 'Other']);
            $table->string('email', 100);
            $table->string('phone', 15);
            $table->text('address');
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('postal_code', 20);
            $table->string('id_type', 20);
            $table->string('id_number', 50);
            $table->integer('status_id');
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            $table->foreign('branch_id')->references('id')->on('branch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};