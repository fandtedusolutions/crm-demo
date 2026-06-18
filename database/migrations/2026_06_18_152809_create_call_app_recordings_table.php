<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_app_recordings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_app_log_id');
            $table->unsignedBigInteger('telecaller_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type', 80);
            $table->unsignedBigInteger('file_size_bytes')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->bigInteger('recorded_at_ms')->nullable();
            $table->timestamps();

            $table->foreign('call_app_log_id')->references('id')->on('call_app_logs')->onDelete('cascade');
            $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('call_app_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_app_recordings');
    }
};
