<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            if (!Schema::hasColumn('labs', 'headerless_margin_top')) {
                $table->integer('headerless_margin_top')->nullable()->default(40);
            }
            if (!Schema::hasColumn('labs', 'headerless_margin_bottom')) {
                $table->integer('headerless_margin_bottom')->nullable()->default(30);
            }
        });
    }

    public function down(): void
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->dropColumn(['headerless_margin_top', 'headerless_margin_bottom']);
        });
    }
};
