<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            if (!Schema::hasColumn('labs', 'signature_width')) {
                $table->integer('signature_width')->nullable()->after('signature_image');
            }
            if (!Schema::hasColumn('labs', 'signature_height')) {
                $table->integer('signature_height')->nullable()->after('signature_width');
            }
            if (!Schema::hasColumn('labs', 'signature_width_2')) {
                $table->integer('signature_width_2')->nullable()->after('signature_image_2');
            }
            if (!Schema::hasColumn('labs', 'signature_height_2')) {
                $table->integer('signature_height_2')->nullable()->after('signature_width_2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->dropColumn(['signature_width', 'signature_height', 'signature_width_2', 'signature_height_2']);
        });
    }
};
