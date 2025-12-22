<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'short_name',
        'unit',
        'normal_range_male',
        'normal_range_female',
        'normal_min',
        'normal_max',
        'price',
        'sample_type',
        'method',
        'instructions',
        'turnaround_time',
        'is_active',
    ];

    protected $casts = [
        'normal_min' => 'decimal:2',
        'normal_max' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function bookingTests(): HasMany
    {
        return $this->hasMany(BookingTest::class);
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(TestParameter::class)->orderBy('sort_order');
    }

    public function hasParameters(): bool
    {
        return $this->parameters()->active()->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNormalRangeAttribute(): string
    {
        if ($this->normal_min !== null && $this->normal_max !== null) {
            return "{$this->normal_min} - {$this->normal_max}";
        }
        return $this->normal_range_male ?? '-';
    }

    public function checkValueInRange(float $value, string $gender = 'male'): string
    {
        if ($this->normal_min === null || $this->normal_max === null) {
            return 'normal';
        }

        $criticalLow = $this->normal_min * 0.5;
        $criticalHigh = $this->normal_max * 1.5;

        if ($value < $criticalLow) {
            return 'critical_low';
        } elseif ($value < $this->normal_min) {
            return 'low';
        } elseif ($value > $criticalHigh) {
            return 'critical_high';
        } elseif ($value > $this->normal_max) {
            return 'high';
        }

        return 'normal';
    }
}
