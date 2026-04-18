<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_code')->unique();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('item_type');
            $table->string('tracking_method');
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('low_stock_threshold')->nullable();
            $table->integer('quantity_total')->nullable()->default(0);
            $table->integer('quantity_available')->nullable()->default(0);
            $table->integer('quantity_issued')->nullable()->default(0);
            $table->integer('quantity_damaged')->nullable()->default(0);
            $table->integer('quantity_under_repair')->nullable()->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->index('department_id');
            $table->index('category_id');
            $table->index('item_type');
            $table->index('tracking_method');
        });

        Schema::create('asset_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->string('serial_number')->nullable()->unique();
            $table->string('asset_tag')->nullable()->unique();
            $table->unsignedBigInteger('assigned_department_id')->nullable();
            $table->unsignedBigInteger('assigned_staff_directory_id')->nullable();
            $table->string('assigned_staff_name_snapshot')->nullable();
            $table->string('condition_status')->default('good');
            $table->string('unit_status')->default('available');
            $table->string('current_location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('assigned_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('assigned_staff_directory_id')->references('id')->on('staff_directory')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->index('unit_status');
        });

        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->unique();
            $table->string('label');
            $table->string('field_type');
            $table->json('options_json')->nullable();
            $table->string('entity_scope')->default('inventory_item');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->date('value_date')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->unique(['custom_field_id', 'entity_type', 'entity_id'], 'cfv_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('asset_units');
        Schema::dropIfExists('inventory_items');
    }
};
