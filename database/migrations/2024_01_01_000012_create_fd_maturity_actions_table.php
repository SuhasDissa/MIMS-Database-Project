<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fd_maturity_actions', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('fd_id');
            $table->dateTime('maturity_date');
            $table->enum('action_taken', ['TRANSFERRED_TO_SAVINGS', 'RENEWED', 'PENDING']);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->bigInteger('new_fd_id')->nullable();
            $table->bigInteger('transaction_id')->nullable();
            $table->timestamp('processed_date')->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->foreign('transaction_id')->references('id')->on('fd_transaction');
            $table->foreign('new_fd_id')->references('id')->on('fixed_deposits');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fd_maturity_actions');
    }
};