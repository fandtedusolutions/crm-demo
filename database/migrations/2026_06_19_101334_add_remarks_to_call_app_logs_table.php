<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('call_app_logs', function (Blueprint $table) {
            $table->string('remarks', 255)->nullable()->after('call_type');
        });

        DB::table('call_app_logs')
            ->where('call_type', 'outgoing')
            ->where('duration_seconds', 0)
            ->whereNull('remarks')
            ->update(['remarks' => 'Not Picked']);
    }

    public function down(): void
    {
        Schema::table('call_app_logs', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
