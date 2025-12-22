<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('normal_min', 10, 2)->nullable();
            $table->decimal('normal_max', 10, 2)->nullable();
            $table->decimal('normal_min_male', 10, 2)->nullable();
            $table->decimal('normal_max_male', 10, 2)->nullable();
            $table->decimal('normal_min_female', 10, 2)->nullable();
            $table->decimal('normal_max_female', 10, 2)->nullable();
            $table->decimal('critical_low', 10, 2)->nullable();
            $table->decimal('critical_high', 10, 2)->nullable();
            $table->string('method')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('group_name')->nullable(); // For grouping like "BLOOD INDICES", "DIFFERENTIAL WBC COUNT"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Update results table to support parameter-level results
        Schema::create('parameter_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_parameter_id')->constrained()->cascadeOnDelete();
            $table->string('value')->nullable();
            $table->decimal('numeric_value', 10, 2)->nullable();
            $table->enum('flag', ['normal', 'low', 'high', 'critical_low', 'critical_high'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_results');
        Schema::dropIfExists('test_parameters');
    }
};
