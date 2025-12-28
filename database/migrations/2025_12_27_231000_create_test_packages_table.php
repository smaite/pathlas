<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Test Packages table
        Schema::create('test_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable(); // Original MRP for discount display
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot table for package-tests relationship
        Schema::create('package_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['test_package_id', 'test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_tests');
        Schema::dropIfExists('test_packages');
    }
};
