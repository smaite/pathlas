<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('test_parameters', 'interpretation')) {
                // Interpretation text shown on report e.g. "Results are within normal limits"
                $table->text('interpretation')->nullable()->after('method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('test_parameters', 'interpretation')) {
                $table->dropColumn('interpretation');
            }
        });
    }
};
