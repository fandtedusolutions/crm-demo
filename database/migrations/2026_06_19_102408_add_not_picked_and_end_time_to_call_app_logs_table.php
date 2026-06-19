<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE call_app_logs MODIFY COLUMN call_type ENUM('incoming', 'outgoing', 'missed', 'rejected', 'not_picked', 'unknown') NOT NULL");

        Schema::table('call_app_logs', function (Blueprint $table) {
            $table->bigInteger('end_at_ms')->nullable()->after('started_at');
            $table->timestamp('ended_at')->nullable()->after('end_at_ms');
        });

        DB::table('call_app_logs')
            ->where('call_type', 'outgoing')
            ->where('duration_seconds', 0)
            ->where(function ($query) {
                $query->whereNull('remarks')->orWhere('remarks', 'Not Picked');
            })
            ->update([
                'call_type' => 'not_picked',
                'remarks' => 'Not Picked',
            ]);
    }

    public function down(): void
    {
        DB::table('call_app_logs')
            ->where('call_type', 'not_picked')
            ->update(['call_type' => 'outgoing']);

        Schema::table('call_app_logs', function (Blueprint $table) {
            $table->dropColumn(['end_at_ms', 'ended_at']);
        });

        DB::statement("ALTER TABLE call_app_logs MODIFY COLUMN call_type ENUM('incoming', 'outgoing', 'missed', 'rejected', 'unknown') NOT NULL");
    }
};
