<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_transaction', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['DEPOSIT', 'WITHDRAWAL', 'TRANSFER']);
            $table->foreignId('from_id')->nullable()->constrained('savings_account');
            $table->foreignId('to_id')->nullable()->constrained('savings_account');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['COMPLETED', 'PENDING', 'FAILED']);
            $table->text('description')->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
            $table->index(['type','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transaction');
    }
};