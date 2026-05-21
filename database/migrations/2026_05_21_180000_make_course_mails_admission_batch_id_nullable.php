<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_mails', function (Blueprint $table) {
            $table->dropForeign(['admission_batch_id']);
        });

        Schema::table('course_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('admission_batch_id')->nullable()->change();
            $table->foreign('admission_batch_id')
                ->references('id')
                ->on('admission_batches')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('course_mails', function (Blueprint $table) {
            $table->dropForeign(['admission_batch_id']);
        });

        Schema::table('course_mails', function (Blueprint $table) {
            $table->unsignedBigInteger('admission_batch_id')->nullable(false)->change();
            $table->foreign('admission_batch_id')
                ->references('id')
                ->on('admission_batches')
                ->cascadeOnDelete();
        });
    }
};
