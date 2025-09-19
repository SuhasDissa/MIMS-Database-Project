<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_account', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 256);
            $table->foreignId('account_type_id')->constrained('savings_account_type');
            $table->foreignId('branch_id')->constrained('branch');
            $table->decimal('balance', 15, 2);
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->date('opened_date');
            $table->date('closed_date')->nullable();
            $table->date('last_transaction_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_account');
    }
};