<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('leads_details', 'programme_type')) {
            DB::statement('ALTER TABLE leads_details MODIFY programme_type VARCHAR(50) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leads_details', 'programme_type')) {
            DB::statement("ALTER TABLE leads_details MODIFY programme_type ENUM('online', 'offline') NULL");
        }
    }
};
