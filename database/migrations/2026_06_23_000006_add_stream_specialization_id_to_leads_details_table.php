<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->foreignId('stream_specialization_id')
                ->nullable()
                ->after('plustwo_subject')
                ->constrained('stream_specializations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads_details', function (Blueprint $table) {
            $table->dropForeign(['stream_specialization_id']);
            $table->dropColumn('stream_specialization_id');
        });
    }
};
