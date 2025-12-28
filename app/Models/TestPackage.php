<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TestPackage extends Model
{
    protected $fillable = [
        'lab_id',
        'name',
        'code',
        'description',
        'price',
        'mrp',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'package_tests')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Calculate discount percentage
    public function getDiscountPercentAttribute()
    {
        if ($this->mrp && $this->mrp > $this->price) {
            return round((($this->mrp - $this->price) / $this->mrp) * 100);
        }
        return 0;
    }

    // Get total value of individual tests
    public function getTestsTotalAttribute()
    {
        return $this->tests->sum('price');
    }

    // Get savings amount
    public function getSavingsAttribute()
    {
        return $this->tests_total - $this->price;
    }
}
