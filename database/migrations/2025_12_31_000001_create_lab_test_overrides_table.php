<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lab Test Overrides - Stores per-lab test customizations using JSON
     * 
     * Optimized approach:
     * - Single JSON column for all overrides (price, name, ranges, etc.)
     * - Only stores changed values (minimal storage)
     * - Flexible - add new fields without migration
     */
    public function up(): void
    {
        Schema::create('lab_test_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->json('overrides')->nullable(); // {"price": 1000, "name": "Custom Name"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['lab_id', 'test_id']);
            $table->index('lab_id');
        });

        // For parameter-level overrides (same optimized approach)
        Schema::create('lab_parameter_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_parameter_id')->constrained()->onDelete('cascade');
            $table->json('overrides')->nullable(); // {"unit": "g/dL", "normal_range": "12-16"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['lab_id', 'test_parameter_id']);
            $table->index('lab_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_parameter_overrides');
        Schema::dropIfExists('lab_test_overrides');
    }
};
