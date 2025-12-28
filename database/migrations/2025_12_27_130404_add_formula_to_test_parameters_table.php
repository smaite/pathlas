<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('test_parameters', 'formula')) {
                $table->string('formula')->nullable()->after('method');
            }
            if (!Schema::hasColumn('test_parameters', 'formula_dependencies')) {
                $table->string('formula_dependencies')->nullable()->after('formula');
            }
            if (!Schema::hasColumn('test_parameters', 'is_calculated')) {
                $table->boolean('is_calculated')->default(false)->after('formula_dependencies');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('test_parameters', 'formula')) {
                $table->dropColumn('formula');
            }
            if (Schema::hasColumn('test_parameters', 'formula_dependencies')) {
                $table->dropColumn('formula_dependencies');
            }
            if (Schema::hasColumn('test_parameters', 'is_calculated')) {
                $table->dropColumn('is_calculated');
            }
        });
    }
};
