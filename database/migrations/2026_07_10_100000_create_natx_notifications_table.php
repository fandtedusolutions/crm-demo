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
        Schema::create('natx_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['low', 'medium', 'high'])->default('medium');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->date('upto_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['is_active', 'upto_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('natx_notifications');
    }
};
