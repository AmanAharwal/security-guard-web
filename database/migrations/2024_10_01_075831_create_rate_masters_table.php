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
        Schema::create('rate_masters', function (Blueprint $table) {
            $table->id();
            $table->string('guard_type');
            $table->decimal('regular_rate')->nullable();
            $table->decimal('laundry_allowance')->nullable();
            $table->decimal('canine_premium')->nullable();
            $table->decimal('fire_arm_premium')->nullable();
            $table->decimal('gross_hourly_rate')->nullable();
            $table->decimal('overtime_rate')->nullable();
            $table->decimal('holiday_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_masters');
    }
};
