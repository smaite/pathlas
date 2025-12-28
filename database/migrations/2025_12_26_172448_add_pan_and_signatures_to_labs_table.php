<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            if (!Schema::hasColumn('labs', 'pan_number')) {
                $table->string('pan_number', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('labs', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('labs', 'signature_name')) {
                $table->string('signature_name', 100)->nullable()->after('signature_image');
            }
            if (!Schema::hasColumn('labs', 'signature_designation')) {
                $table->string('signature_designation', 100)->nullable()->after('signature_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $columns = ['pan_number', 'signature_image', 'signature_name', 'signature_designation'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('labs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
