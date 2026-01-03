<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'lab_id',
        'patient_id',
        'created_by',
        'referring_doctor',
        'received_by',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'payment_status',
        'sample_collected_at',
        'notes',
        'is_urgent',
        // New fields
        'collection_date',
        'received_date',
        'reporting_date',
        'sample_collected_by',
        'sample_collected_at_address',
        'patient_type',
        'collection_centre',
        'referring_doctor_name',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sample_collected_at' => 'datetime',
        'collection_date' => 'datetime',
        'received_date' => 'datetime',
        'reporting_date' => 'datetime',
        'is_urgent' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_id)) {
                $booking->booking_id = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function referringDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referring_doctor');
    }

    public function bookingTests(): HasMany
    {
        return $this->hasMany(BookingTest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return $this->payments->sum('amount');
    }

    public function getDueAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function updatePaymentStatus(): void
    {
        $paidAmount = $this->paid_amount;
        
        if ($paidAmount >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($paidAmount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
        
        $this->save();
    }

    public function calculateTotal(): void
    {
        $this->subtotal = $this->bookingTests->sum('price');
        $this->total_amount = $this->subtotal - $this->discount + $this->tax;
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'sample_collected', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'sample_collected' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
