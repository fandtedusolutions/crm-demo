<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_postsale')->default(false)->after('is_b2b');
            $table->boolean('is_postsale_gm')->default(false)->after('is_postsale');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('is_postsale')->default(false)->after('is_b2b');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_postsale')->default(false)->after('is_b2b');
        });

        Schema::table('converted_leads', function (Blueprint $table) {
            $table->boolean('is_postsale')->default(false)->after('is_b2b');
        });
    }

    public function down(): void
    {
        Schema::table('converted_leads', function (Blueprint $table) {
            $table->dropColumn('is_postsale');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('is_postsale');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('is_postsale');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_postsale', 'is_postsale_gm']);
        });
    }
};
