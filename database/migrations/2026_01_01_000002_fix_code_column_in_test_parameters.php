<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE test_parameters MODIFY code VARCHAR(255) NULL DEFAULT NULL');
    }

    public function down(): void
    {
        // No rollback needed
    }
};
