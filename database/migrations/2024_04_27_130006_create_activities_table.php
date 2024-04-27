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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roaster_id')->constrained('roasters')->onDelete('cascade');
            $table->string('activity');
            $table->string('code');
            $table->string('description')->nullable();
            $table->string('remark')->nullable();
            $table->string('from')->nullable();
            $table->time('std')->nullable();
            $table->string('to')->nullable();
            $table->time('sta')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('blh', 8, 3)->nullable()->comment('in mins');
            $table->decimal('flight_time', 8, 3)->nullable()->comment('in mins');
            $table->decimal('night_time', 8, 3)->nullable()->comment('in mins');
            $table->decimal('duration', 8, 3)->nullable()->comment('in mins');
            $table->string('ext')->nullable();
            $table->string('pax_booked')->nullable();
            $table->string('ac_reg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
