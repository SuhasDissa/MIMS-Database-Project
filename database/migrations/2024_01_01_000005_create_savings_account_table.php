<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_account', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('account_number', 256);
            $table->bigInteger('account_type_id');
            $table->integer('branch_id');
            $table->decimal('balance', 15, 2);
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->date('opened_date');
            $table->date('closed_date')->nullable();
            $table->date('last_transaction_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            
            $table->foreign('account_type_id')->references('id')->on('savings_account_type');
            $table->foreign('branch_id')->references('id')->on('branch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_account');
    }
};