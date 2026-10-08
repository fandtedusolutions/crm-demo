<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'hide_from_team_lead')) {
                $table->boolean('hide_from_team_lead')->default(false)->after('is_postsale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'hide_from_team_lead')) {
                $table->dropColumn('hide_from_team_lead');
            }
        });
    }
};
