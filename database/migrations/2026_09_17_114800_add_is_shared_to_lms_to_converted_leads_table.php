<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('converted_leads', 'is_shared_to_lms')) {
                $table->boolean('is_shared_to_lms')->default(false)->after('is_course_changed');
            }
            if (! Schema::hasColumn('converted_leads', 'lms_stream_id')) {
                $table->unsignedBigInteger('lms_stream_id')->nullable()->after('is_shared_to_lms');
            }
            if (! Schema::hasColumn('converted_leads', 'shared_to_lms_at')) {
                $table->timestamp('shared_to_lms_at')->nullable()->after('lms_stream_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('converted_leads', 'shared_to_lms_at') ? 'shared_to_lms_at' : null,
                Schema::hasColumn('converted_leads', 'lms_stream_id') ? 'lms_stream_id' : null,
                Schema::hasColumn('converted_leads', 'is_shared_to_lms') ? 'is_shared_to_lms' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
