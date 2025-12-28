<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $fillable = [
        'name',
        'code',
        'tagline',
        'address',
        'city',
        'state',
        'pincode',
        'phone',
        'phone2',
        'email',
        'pan_number',
        'website',
        'logo',
        'logo_width',
        'logo_height',
        'signature_image',
        'signature_width',
        'signature_height',
        'signature_name',
        'signature_designation',
        'signature_image_2',
        'signature_width_2',
        'signature_height_2',
        'signature_name_2',
        'signature_designation_2',
        'header_color',
        'footer_note',
        'report_notes',
        'subscription_plan',
        'subscription_starts_at',
        'subscription_expires_at',
        'subscription_amount',
        'subscription_notes',
        'is_verified',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'require_approval',
        'headerless_margin_top',
        'headerless_margin_bottom',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'require_approval' => 'boolean',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'subscription_amount' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->pincode]);
        return implode(', ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_verified', false)->whereNull('rejection_reason');
    }

    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_plan === 'lifetime') {
            return true;
        }

        if (!$this->subscription_expires_at) {
            return false;
        }

        return $this->subscription_expires_at->isFuture();
    }

    public function getSubscriptionStatusAttribute(): string
    {
        if ($this->subscription_plan === 'lifetime') {
            return 'active';
        }

        if (!$this->subscription_expires_at) {
            return 'inactive';
        }

        if ($this->subscription_expires_at->isPast()) {
            return 'expired';
        }

        if ($this->subscription_expires_at->diffInDays(now()) <= 30) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function getSubscriptionBadgeAttribute(): string
    {
        return match($this->subscription_status) {
            'active' => 'bg-green-100 text-green-700',
            'expiring_soon' => 'bg-yellow-100 text-yellow-700',
            'expired' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->subscription_plan === 'lifetime') {
            return null;
        }

        if (!$this->subscription_expires_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->subscription_expires_at, false));
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
