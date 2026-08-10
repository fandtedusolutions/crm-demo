<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('leads', 'first_lead_source_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedBigInteger('first_lead_source_id')->nullable()->after('lead_source_id');
            });
        }

        if (! Schema::hasColumn('leads', 'first_lead_course_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedBigInteger('first_lead_course_id')->nullable()->after('course_id');
            });
        }

        if (! Schema::hasColumn('leads', 'first_lead_status_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedBigInteger('first_lead_status_id')->nullable()->after('lead_status_id');
            });
        }

        $this->addForeignKeyIfMissing('leads', 'first_lead_source_id', 'lead_sources');
        $this->addForeignKeyIfMissing('leads', 'first_lead_course_id', 'courses');
        $this->addForeignKeyIfMissing('leads', 'first_lead_status_id', 'lead_statuses');

        DB::table('leads')->whereNull('first_lead_source_id')->update([
            'first_lead_source_id' => DB::raw('lead_source_id'),
        ]);
        DB::table('leads')->whereNull('first_lead_course_id')->update([
            'first_lead_course_id' => DB::raw('course_id'),
        ]);
        DB::table('leads')->whereNull('first_lead_status_id')->update([
            'first_lead_status_id' => DB::raw('lead_status_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            foreach (['first_lead_source_id', 'first_lead_course_id', 'first_lead_status_id'] as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Throwable $e) {
                        // Foreign key may not exist
                    }
                }
            }

            $columns = collect([
                'first_lead_source_id',
                'first_lead_course_id',
                'first_lead_status_id',
            ])->filter(fn ($column) => Schema::hasColumn('leads', $column))->values()->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    private function addForeignKeyIfMissing(string $table, string $column, string $referencesTable): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        $constraint = $table . '_' . $column . '_foreign';
        $exists = DB::selectOne(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = ?',
            [$table, $constraint, 'FOREIGN KEY']
        );

        if ($exists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column, $referencesTable) {
            $blueprint->foreign($column)->references('id')->on($referencesTable)->nullOnDelete();
        });
    }
};
