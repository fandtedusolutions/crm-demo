<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'is_transfered')) {
                $table->boolean('is_transfered')->default(false)->after('status');
            }
            if (! Schema::hasColumn('invoices', 'transfered_to_invoice_id')) {
                $table->unsignedBigInteger('transfered_to_invoice_id')->nullable()->after('is_transfered');
                $table->foreign('transfered_to_invoice_id')
                    ->references('id')
                    ->on('invoices')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'transfered_at')) {
                $table->timestamp('transfered_at')->nullable()->after('transfered_to_invoice_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'transfered_to_invoice_id')) {
                $table->dropForeign(['transfered_to_invoice_id']);
                $table->dropColumn('transfered_to_invoice_id');
            }
            if (Schema::hasColumn('invoices', 'transfered_at')) {
                $table->dropColumn('transfered_at');
            }
            if (Schema::hasColumn('invoices', 'is_transfered')) {
                $table->dropColumn('is_transfered');
            }
        });
    }
};
