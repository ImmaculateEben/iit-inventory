<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_records', function (Blueprint $table) {
            $table->string('component_repaired')->nullable()->after('problem_description');
            $table->text('repair_description')->nullable()->after('component_repaired');
            $table->date('repair_date')->nullable()->after('date_reported');
        });
    }

    public function down(): void
    {
        Schema::table('repair_records', function (Blueprint $table) {
            $table->dropColumn(['component_repaired', 'repair_description', 'repair_date']);
        });
    }
};
