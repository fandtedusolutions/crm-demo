<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_app_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telecaller_id');
            $table->string('device_id', 100);
            $table->string('device_call_id', 120);
            $table->string('phone_number', 30);
            $table->string('contact_name', 150)->nullable();
            $table->enum('call_type', ['incoming', 'outgoing', 'missed', 'rejected', 'unknown']);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->bigInteger('started_at_ms');
            $table->timestamp('started_at')->nullable();
            $table->boolean('has_recording')->default(false);
            $table->boolean('recording_uploaded')->default(false);
            $table->unsignedInteger('recording_duration_seconds')->nullable();
            $table->string('recording_file_name', 255)->nullable();
            $table->string('app_version', 20)->nullable();
            $table->timestamps();

            $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['telecaller_id', 'device_call_id'], 'call_app_logs_telecaller_device_call_unique');
            $table->index(['telecaller_id', 'started_at_ms'], 'call_app_logs_telecaller_started_idx');
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_app_logs');
    }
};
