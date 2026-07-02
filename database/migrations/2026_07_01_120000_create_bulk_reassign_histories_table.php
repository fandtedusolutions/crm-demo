<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_reassign_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_telecaller_id');
            $table->unsignedBigInteger('to_telecaller_id');
            $table->unsignedInteger('leads_count')->default(0);
            $table->unsignedBigInteger('lead_source_id');
            $table->unsignedBigInteger('lead_status_id');
            $table->date('lead_from_date');
            $table->date('lead_to_date');
            $table->date('reassign_date');
            $table->time('reassign_time');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('from_telecaller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_telecaller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lead_source_id')->references('id')->on('lead_sources')->onDelete('cascade');
            $table->foreign('lead_status_id')->references('id')->on('lead_statuses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['reassign_date', 'to_telecaller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_reassign_histories');
    }
};
