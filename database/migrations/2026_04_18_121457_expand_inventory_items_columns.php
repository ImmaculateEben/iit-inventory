<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Procurement / sourcing
            $table->string('manufacturer')->nullable()->after('description');
            $table->string('model_number')->nullable()->after('manufacturer');
            $table->string('supplier_donor')->nullable()->after('model_number');
            $table->date('purchase_date')->nullable()->after('supplier_donor');
            $table->decimal('purchase_cost', 12, 2)->nullable()->after('purchase_date');
            $table->string('warranty_info')->nullable()->after('purchase_cost');
            $table->date('warranty_expiry')->nullable()->after('warranty_info');
            $table->string('guarantee_info')->nullable()->after('warranty_expiry');

            // Structured location
            $table->string('floor')->nullable()->after('location');
            $table->string('venue')->nullable()->after('floor');
            $table->string('venue_storage')->nullable()->after('venue');

            // Additional item details
            $table->string('unit_of_measure')->nullable()->after('tracking_method');
            $table->string('size')->nullable()->after('venue_storage');
            $table->text('remarks')->nullable()->after('size');

            // Stock fields that were referenced but missing
            $table->integer('quantity_in_stock')->nullable()->default(0)->after('low_stock_threshold');
            $table->integer('reorder_level')->nullable()->default(0)->after('quantity_in_stock');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn([
                'manufacturer', 'model_number', 'supplier_donor',
                'purchase_date', 'purchase_cost', 'warranty_info', 'warranty_expiry', 'guarantee_info',
                'floor', 'venue', 'venue_storage',
                'unit_of_measure', 'size', 'remarks',
                'quantity_in_stock', 'reorder_level',
            ]);
        });
    }
};
