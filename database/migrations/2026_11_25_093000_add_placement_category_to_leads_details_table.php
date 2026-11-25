<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            if (! Schema::hasColumn('leads_details', 'placement_category')) {
                $table->string('placement_category', 40)->nullable()->after('programme_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            if (Schema::hasColumn('leads_details', 'placement_category')) {
                $table->dropColumn('placement_category');
            }
        });
    }
};
