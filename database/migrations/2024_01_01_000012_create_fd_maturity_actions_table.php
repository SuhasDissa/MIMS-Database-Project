<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fd_maturity_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fd_id')->constrained('fixed_deposits');
            $table->dateTime('maturity_date');
            $table->enum('action_taken', ['TRANSFERRED_TO_SAVINGS', 'RENEWED', 'PENDING']);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('new_fd_id')->nullable()->constrained('fixed_deposits');
            $table->foreignId('transaction_id')->nullable()->constrained('fd_transaction');
            $table->timestamp('processed_date')->nullable();
            $table->timestamps();
            $table->index(['fd_id','action_taken']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fd_maturity_actions');
    }
};