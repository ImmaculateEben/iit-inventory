<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing indexes for performance and DoS prevention
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index('archived_at');
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('issue_records', function (Blueprint $table) {
            $table->index('issued_by_user_id');
            $table->index('issued_at');
        });

        Schema::table('return_records', function (Blueprint $table) {
            $table->index('received_by_user_id');
            $table->index('returned_at');
        });

        Schema::table('repair_records', function (Blueprint $table) {
            $table->index('created_by_user_id');
            $table->index('department_id');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->index('performed_by_user_id');
            $table->index('performed_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        Schema::table('issue_records', function (Blueprint $table) {
            $table->dropIndex(['issued_by_user_id']);
            $table->dropIndex(['issued_at']);
        });

        Schema::table('return_records', function (Blueprint $table) {
            $table->dropIndex(['received_by_user_id']);
            $table->dropIndex(['returned_at']);
        });

        Schema::table('repair_records', function (Blueprint $table) {
            $table->dropIndex(['created_by_user_id']);
            $table->dropIndex(['department_id']);
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropIndex(['performed_by_user_id']);
            $table->dropIndex(['performed_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
        });
    }
};
