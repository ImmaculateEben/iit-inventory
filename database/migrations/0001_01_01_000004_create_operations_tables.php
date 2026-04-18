<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('requested_by_user_id')->constrained('users');
            $table->string('status')->default('pending');
            $table->timestamp('requested_at');
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->foreign('approved_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
        });

        Schema::create('request_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained();
            $table->unsignedInteger('requested_quantity');
            $table->unsignedInteger('fulfilled_quantity')->default(0);
            $table->unsignedBigInteger('requested_staff_directory_id')->nullable();
            $table->string('requested_staff_name_snapshot')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('requested_staff_directory_id')->references('id')->on('staff_directory')->nullOnDelete();
        });

        Schema::create('issue_records', function (Blueprint $table) {
            $table->id();
            $table->string('issue_number')->unique();
            $table->unsignedBigInteger('request_line_id')->nullable();
            $table->foreignId('inventory_item_id')->constrained();
            $table->unsignedBigInteger('asset_unit_id')->nullable();
            $table->foreignId('department_id')->constrained();
            $table->unsignedBigInteger('staff_directory_id')->nullable();
            $table->string('staff_name_snapshot')->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->foreignId('issued_by_user_id')->constrained('users');
            $table->timestamp('issued_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('request_line_id')->references('id')->on('request_lines')->nullOnDelete();
            $table->foreign('asset_unit_id')->references('id')->on('asset_units')->nullOnDelete();
            $table->foreign('staff_directory_id')->references('id')->on('staff_directory')->nullOnDelete();
            $table->index('department_id');
        });

        Schema::create('return_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_record_id')->constrained();
            $table->foreignId('inventory_item_id')->constrained();
            $table->unsignedBigInteger('asset_unit_id')->nullable();
            $table->foreignId('department_id')->constrained();
            $table->unsignedBigInteger('staff_directory_id')->nullable();
            $table->string('staff_name_snapshot')->nullable();
            $table->unsignedInteger('returned_quantity');
            $table->string('return_condition');
            $table->foreignId('received_by_user_id')->constrained('users');
            $table->timestamp('returned_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('asset_unit_id')->references('id')->on('asset_units')->nullOnDelete();
            $table->foreign('staff_directory_id')->references('id')->on('staff_directory')->nullOnDelete();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();
            $table->foreignId('inventory_item_id')->constrained();
            $table->string('action_type');
            $table->integer('delta_total')->default(0);
            $table->integer('delta_available')->default(0);
            $table->integer('delta_issued')->default(0);
            $table->integer('delta_damaged')->default(0);
            $table->integer('delta_under_repair')->default(0);
            $table->foreignId('performed_by_user_id')->constrained('users');
            $table->timestamp('performed_at');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('related_issue_record_id')->nullable();
            $table->unsignedBigInteger('related_return_record_id')->nullable();
            $table->unsignedBigInteger('related_repair_record_id')->nullable();
            $table->timestamps();

            $table->foreign('related_issue_record_id')->references('id')->on('issue_records')->nullOnDelete();
            $table->foreign('related_return_record_id')->references('id')->on('return_records')->nullOnDelete();
        });

        Schema::create('repair_records', function (Blueprint $table) {
            $table->id();
            $table->string('repair_number')->unique();
            $table->foreignId('inventory_item_id')->constrained();
            $table->unsignedBigInteger('asset_unit_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->text('problem_description');
            $table->text('repair_notes')->nullable();
            $table->date('date_reported');
            $table->date('date_sent')->nullable();
            $table->date('date_returned')->nullable();
            $table->string('status')->default('reported');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('asset_unit_id')->references('id')->on('asset_units')->nullOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('updated_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Add repair FK on stock_adjustments
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreign('related_repair_record_id')->references('id')->on('repair_records')->nullOnDelete();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('action_code');
            $table->string('target_type');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('summary');
            $table->json('metadata_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['target_type', 'target_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['related_repair_record_id']);
        });
        Schema::dropIfExists('repair_records');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('return_records');
        Schema::dropIfExists('issue_records');
        Schema::dropIfExists('request_lines');
        Schema::dropIfExists('inventory_requests');
    }
};
