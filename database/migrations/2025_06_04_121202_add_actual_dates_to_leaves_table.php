<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
        });
    }
};
