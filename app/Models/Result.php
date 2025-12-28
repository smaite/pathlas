<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_test_id',
        'value',
        'numeric_value',
        'flag',
        'remarks',
        'entered_by',
        'entered_at',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'status',
        // Edit tracking
        'edited_at',
        'edited_by',
        'edit_reason',
        'previous_value',
    ];

    protected $casts = [
        'numeric_value' => 'decimal:2',
        'entered_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function bookingTest(): BelongsTo
    {
        return $this->belongsTo(BookingTest::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function calculateFlag(): void
    {
        $test = $this->bookingTest->test;
        $patient = $this->bookingTest->booking->patient;
        
        if ($this->numeric_value !== null) {
            $this->flag = $test->checkValueInRange($this->numeric_value, $patient->gender);
        }
    }

    public function getFlagBadgeAttribute(): string
    {
        return match($this->flag) {
            'low' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical_low' => 'bg-red-100 text-red-800',
            'critical_high' => 'bg-red-100 text-red-800',
            default => 'bg-green-100 text-green-800',
        };
    }

    public function getFlagLabelAttribute(): string
    {
        return match($this->flag) {
            'low' => 'L',
            'high' => 'H',
            'critical_low' => 'LL',
            'critical_high' => 'HH',
            default => '',
        };
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', ['entered', 'verified']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
