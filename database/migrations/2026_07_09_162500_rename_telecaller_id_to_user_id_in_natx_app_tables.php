<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('natx_app_logs')
            && Schema::hasColumn('natx_app_logs', 'telecaller_id')
            && !Schema::hasColumn('natx_app_logs', 'user_id')) {
            Schema::table('natx_app_logs', function (Blueprint $table) {
                $table->dropForeign(['telecaller_id']);
                $table->dropUnique('natx_app_logs_telecaller_device_call_unique');
                $table->dropIndex('natx_app_logs_telecaller_started_idx');
                $table->renameColumn('telecaller_id', 'user_id');
            });

            Schema::table('natx_app_logs', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['user_id', 'device_call_id'], 'natx_app_logs_user_device_call_unique');
                $table->index(['user_id', 'started_at_ms'], 'natx_app_logs_user_started_idx');
            });
        }

        if (Schema::hasTable('natx_app_recordings')
            && Schema::hasColumn('natx_app_recordings', 'telecaller_id')
            && !Schema::hasColumn('natx_app_recordings', 'user_id')) {
            Schema::table('natx_app_recordings', function (Blueprint $table) {
                $table->dropForeign(['telecaller_id']);
                $table->renameColumn('telecaller_id', 'user_id');
            });

            Schema::table('natx_app_recordings', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('natx_app_recordings')
            && Schema::hasColumn('natx_app_recordings', 'user_id')
            && !Schema::hasColumn('natx_app_recordings', 'telecaller_id')) {
            Schema::table('natx_app_recordings', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->renameColumn('user_id', 'telecaller_id');
            });

            Schema::table('natx_app_recordings', function (Blueprint $table) {
                $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('natx_app_logs')
            && Schema::hasColumn('natx_app_logs', 'user_id')
            && !Schema::hasColumn('natx_app_logs', 'telecaller_id')) {
            Schema::table('natx_app_logs', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropUnique('natx_app_logs_user_device_call_unique');
                $table->dropIndex('natx_app_logs_user_started_idx');
                $table->renameColumn('user_id', 'telecaller_id');
            });

            Schema::table('natx_app_logs', function (Blueprint $table) {
                $table->foreign('telecaller_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['telecaller_id', 'device_call_id'], 'natx_app_logs_telecaller_device_call_unique');
                $table->index(['telecaller_id', 'started_at_ms'], 'natx_app_logs_telecaller_started_idx');
            });
        }
    }
};
