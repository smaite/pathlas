<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (!Schema::hasColumn('results', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('results', 'edited_by')) {
                $table->foreignId('edited_by')->nullable()->after('edited_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('results', 'edit_reason')) {
                $table->string('edit_reason')->nullable()->after('edited_by');
            }
            if (!Schema::hasColumn('results', 'previous_value')) {
                $table->string('previous_value')->nullable()->after('edit_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $columns = ['edited_at', 'edited_by', 'edit_reason', 'previous_value'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('results', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
