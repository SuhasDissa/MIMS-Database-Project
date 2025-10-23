<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('fd_number', 256);
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('fd_type_id')->constrained('fixed_deposit_type');
            $table->foreignId('branch_id')->constrained('branch');
            $table->foreignId('linked_account_id')->nullable()->constrained('savings_account');
            $table->decimal('principal_amount', 15, 2);
            $table->enum('interest_freq', ['MONTHLY', 'END']);
            $table->decimal('maturity_amount', 15, 2);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->enum('status', ['ACTIVE', 'MATURED', 'PREMATURELY_CLOSED']);
            $table->enum('interest_payout_option', ['TRANSFER_TO_SAVINGS', 'RENEW_FD']);
            $table->boolean('auto_renewal')->default(false);
            $table->date('closed_date')->nullable();
            $table->timestamps();
            $table->index(['fd_number','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_deposits');
    }
};