<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('test_parameters', 'normal_range_male')) {
                $table->string('normal_range_male')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('test_parameters', 'normal_range_female')) {
                $table->string('normal_range_female')->nullable()->after('normal_range_male');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->dropColumn(['normal_range_male', 'normal_range_female']);
        });
    }
};
