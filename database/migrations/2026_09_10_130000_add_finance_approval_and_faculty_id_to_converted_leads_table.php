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
        Schema::table('converted_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('converted_leads', 'finance_approval')) {
                $table->string('finance_approval')->default('Pending')->after('admission_batch_id');
            }
            if (! Schema::hasColumn('converted_leads', 'faculty_id')) {
                $table->foreignId('faculty_id')->nullable()->after('finance_approval')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            if (Schema::hasColumn('converted_leads', 'faculty_id')) {
                $table->dropForeign(['faculty_id']);
                $table->dropColumn('faculty_id');
            }
            if (Schema::hasColumn('converted_leads', 'finance_approval')) {
                $table->dropColumn('finance_approval');
            }
        });
    }
};
