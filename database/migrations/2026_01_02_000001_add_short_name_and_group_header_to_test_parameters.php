<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('test_parameters', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('name');
            }
            if (!Schema::hasColumn('test_parameters', 'is_group_header')) {
                $table->boolean('is_group_header')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            $table->dropColumn(['short_name', 'is_group_header']);
        });
    }
};
