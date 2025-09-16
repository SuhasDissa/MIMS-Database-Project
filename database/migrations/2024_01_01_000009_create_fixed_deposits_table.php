<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_deposits', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('fd_number', 256);
            $table->bigInteger('customer_id');
            $table->bigInteger('fd_type_id');
            $table->integer('branch_id');
            $table->bigInteger('linked_account_id')->nullable();
            $table->decimal('principal_amount', 15, 2);
            $table->enum('interest_freq', ['MONTHLY', 'END']);
            $table->decimal('maturity_amount', 15, 2);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->enum('status', ['ACTIVE', 'MATURED', 'PREMATURELY_CLOSED']);
            $table->enum('interest_payout_option', ['TRANSFER_TO_SAVINGS', 'RENEW_FD']);
            $table->boolean('auto_renewal')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->date('closed_date')->nullable();
            
            $table->foreign('branch_id')->references('id')->on('branch');
            $table->foreign('fd_type_id')->references('id')->on('fixed_deposit_type');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_deposits');
    }
};