<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('tagline')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('header_color')->default('#1e3a8a');
            $table->text('footer_note')->nullable();
            $table->text('report_notes')->nullable();
            
            // Subscription fields
            $table->enum('subscription_plan', ['free_trial', 'monthly', 'yearly', 'lifetime', 'custom'])->default('free_trial');
            $table->datetime('subscription_starts_at')->nullable();
            $table->datetime('subscription_expires_at')->nullable();
            $table->decimal('subscription_amount', 10, 2)->default(0);
            $table->text('subscription_notes')->nullable();
            
            // Verification
            $table->boolean('is_verified')->default(false);
            $table->datetime('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            
            // Settings
            $table->boolean('require_approval')->default(false); // Off by default
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

        // Add lab_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('lab_id')->nullable()->after('role_id')->constrained('labs')->nullOnDelete();
        });

        // Add lab_id to patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('lab_id')->nullable()->after('id')->constrained('labs')->nullOnDelete();
        });

        // Add lab_id to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('lab_id')->nullable()->after('id')->constrained('labs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['lab_id']);
            $table->dropColumn('lab_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['lab_id']);
            $table->dropColumn('lab_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lab_id']);
            $table->dropColumn('lab_id');
        });

        Schema::dropIfExists('labs');
    }
};
