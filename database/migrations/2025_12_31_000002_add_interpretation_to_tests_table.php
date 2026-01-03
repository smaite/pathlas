<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add interpretation/clinical notes field to tests table
     * This is for test-level interpretation (like glucose clinical notes)
     */
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (!Schema::hasColumn('tests', 'interpretation')) {
                $table->text('interpretation')->nullable()->after('instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            if (Schema::hasColumn('tests', 'interpretation')) {
                $table->dropColumn('interpretation');
            }
        });
    }
};
