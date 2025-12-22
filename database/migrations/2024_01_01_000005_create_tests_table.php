<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('test_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('short_name')->nullable();
            $table->string('unit')->nullable();
            $table->string('normal_range_male')->nullable();
            $table->string('normal_range_female')->nullable();
            $table->decimal('normal_min', 10, 2)->nullable();
            $table->decimal('normal_max', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('sample_type', ['blood', 'urine', 'stool', 'swab', 'other'])->default('blood');
            $table->text('method')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('turnaround_time')->default(24);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
