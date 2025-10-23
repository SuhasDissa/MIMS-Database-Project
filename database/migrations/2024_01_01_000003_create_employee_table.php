<?php

use App\Enums\EmployeePosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100);
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 15);
            $table->enum('position', EmployeePosition::values());
            $table->string('nic_num', 12);
            $table->foreignId('branch_id')->constrained('branch');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(["first_name","last_name"]) ;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};