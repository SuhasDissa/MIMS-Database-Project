<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fd_transaction', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->enum('type', ['DEPOSIT', 'WITHDRAWAL']);
            $table->enum('method', ['ACCOUNT', 'CASH']);
            $table->bigInteger('fd_acc_id');
            $table->decimal('amount', 15, 2);
            $table->string('description', 256)->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fd_transaction');
    }
};