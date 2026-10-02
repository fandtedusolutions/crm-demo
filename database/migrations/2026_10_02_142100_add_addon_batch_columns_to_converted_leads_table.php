<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('addon_batch_id')->nullable()->after('batch_id');
            $table->unsignedBigInteger('addon_admission_batch_id')->nullable()->after('admission_batch_id');

            $table->foreign('addon_batch_id')->references('id')->on('batches')->onDelete('set null');
            $table->foreign('addon_admission_batch_id')->references('id')->on('admission_batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropForeign(['addon_batch_id']);
            $table->dropForeign(['addon_admission_batch_id']);
            $table->dropColumn(['addon_batch_id', 'addon_admission_batch_id']);
        });
    }
};
