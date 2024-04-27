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
        Schema::create('roasters', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('blh', 8, 3)->nullable()->comment('in mins');
            $table->decimal('flight_time', 8, 3)->nullable()->comment('in mins');
            $table->decimal('night_time', 8, 3)->nullable()->comment('in mins');
            $table->decimal('duration', 8, 3)->nullable()->comment('in mins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roasters');
    }
};
