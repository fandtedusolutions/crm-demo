<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('natx_work_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->string('slot', 20);
            $table->unsignedBigInteger('updated_at_ms');
            $table->dateTime('completed_at');
            $table->string('device_id', 64);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'work_date', 'slot'], 'natx_work_status_user_date_slot_unique');
            $table->index(['user_id', 'work_date'], 'natx_work_status_user_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('natx_work_status');
    }
};
