<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            if (!Schema::hasColumn('labs', 'signature_name_2')) {
                $table->string('signature_name_2')->nullable()->after('signature_designation');
            }
            if (!Schema::hasColumn('labs', 'signature_designation_2')) {
                $table->string('signature_designation_2')->nullable()->after('signature_name_2');
            }
            if (!Schema::hasColumn('labs', 'signature_image_2')) {
                $table->string('signature_image_2')->nullable()->after('signature_designation_2');
            }
            if (!Schema::hasColumn('labs', 'logo_width')) {
                $table->integer('logo_width')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('labs', 'logo_height')) {
                $table->integer('logo_height')->nullable()->after('logo_width');
            }
        });
    }

    public function down(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->dropColumn(['signature_name_2', 'signature_designation_2', 'signature_image_2', 'logo_width', 'logo_height']);
        });
    }
};
