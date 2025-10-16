<?php

use App\Models\CustomerStatusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_account_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('customer_status_id')->nullable()->constrained('customer_status_types');
            $table->decimal('min_balance', 15, 2);
            $table->decimal('interest_rate', 5, 4);
            $table->string('description', 256)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_account_type');
    }
};