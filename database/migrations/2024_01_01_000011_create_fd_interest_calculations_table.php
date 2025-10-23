<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fd_interest_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('fixed_deposits');
            $table->date('calculation_period_start');
            $table->date('calculation_period_end');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 4);
            $table->integer('days_calculated');
            $table->decimal('interest_amount', 15, 2);
            $table->enum('status', ['CALCULATED', 'CREDITED', 'FAILED']);
            $table->timestamp('calculation_date')->nullable();
            $table->timestamp('credited_date')->nullable();
            $table->timestamps();
            $table->foreignId('transaction_id')->nullable()->constrained('savings_transaction');
            $table->index(['account_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fd_interest_calculations');
    }
};