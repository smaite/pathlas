<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParameterResult extends Model
{
    protected $fillable = [
        'booking_test_id',
        'test_parameter_id',
        'value',
        'numeric_value',
        'flag',
    ];

    public function bookingTest()
    {
        return $this->belongsTo(BookingTest::class);
    }

    public function testParameter()
    {
        return $this->belongsTo(TestParameter::class);
    }

    public function getFlagBadgeAttribute()
    {
        return match($this->flag) {
            'low' => 'text-blue-600',
            'high' => 'text-red-600',
            'critical_low' => 'text-red-700 font-bold',
            'critical_high' => 'text-red-700 font-bold',
            default => 'text-green-600',
        };
    }

    public function getFlagLabelAttribute()
    {
        return match($this->flag) {
            'low' => 'Low',
            'high' => 'High',
            'critical_low' => 'Critical Low',
            'critical_high' => 'Critical High',
            default => 'Normal',
        };
    }
}
