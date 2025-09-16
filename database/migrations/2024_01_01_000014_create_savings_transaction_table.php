<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_transaction', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->enum('type', ['DEPOSIT', 'WITHDRAWAL', 'TRANSFER']);
            $table->bigInteger('from_id')->nullable();
            $table->bigInteger('to_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['COMPLETED', 'PENDING', 'FAILED']);
            $table->text('description')->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();
            
            $table->foreign('from_id')->references('id')->on('savings_account');
            $table->foreign('to_id')->references('id')->on('savings_account');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transaction');
    }
};