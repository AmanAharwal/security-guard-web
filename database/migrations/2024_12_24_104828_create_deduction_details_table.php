<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deduction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guard_id');
            $table->unsignedBigInteger('deduction_id')->nullable();
            $table->date('deduction_date')->nullable();
            $table->decimal('amount_deducted', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();

            $table->timestamps();
            $table->foreign('guard_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deduction_id')->references('id')->on('deductions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_details');
    }
};
