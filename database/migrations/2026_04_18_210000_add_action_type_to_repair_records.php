<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_records', function (Blueprint $table) {
            $table->string('action_type')->default('repair')->after('repair_number');
        });
    }

    public function down(): void
    {
        Schema::table('repair_records', function (Blueprint $table) {
            $table->dropColumn('action_type');
        });
    }
};
