<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_accounts_customers', function (Blueprint $table) {
            $table->bigInteger('sav_acc_id');
            $table->bigInteger('customer_id');
            
            $table->foreign('sav_acc_id')->references('id')->on('savings_account');
            $table->foreign('customer_id')->references('id')->on('customers');
            
            $table->primary(['sav_acc_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_accounts_customers');
    }
};