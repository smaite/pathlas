<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->datetime('collection_date')->nullable()->after('is_urgent');
            $table->datetime('received_date')->nullable()->after('collection_date');
            $table->datetime('reporting_date')->nullable()->after('received_date');
            $table->string('sample_collected_by')->nullable()->after('reporting_date');
            $table->string('sample_collected_at_address')->nullable()->after('sample_collected_by');
            $table->string('patient_type')->default('other')->after('sample_collected_at_address');
            $table->string('collection_centre')->default('Main Branch')->after('patient_type');
            $table->string('referring_doctor_name')->nullable()->after('collection_centre');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'collection_date', 
                'received_date', 
                'reporting_date', 
                'sample_collected_by', 
                'sample_collected_at_address', 
                'patient_type', 
                'collection_centre',
                'referring_doctor_name'
            ]);
        });
    }
};
