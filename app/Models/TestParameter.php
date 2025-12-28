<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestParameter extends Model
{
    protected $fillable = [
        'test_id',
        'name',
        'code',
        'unit',
        'normal_min',
        'normal_max',
        'normal_min_male',
        'normal_max_male',
        'normal_min_female',
        'normal_max_female',
        'critical_low',
        'critical_high',
        'method',
        'interpretation',
        'formula',
        'formula_dependencies',
        'is_calculated',
        'sort_order',
        'group_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function parameterResults()
    {
        return $this->hasMany(ParameterResult::class);
    }

    // Get normal range based on gender
    public function getNormalRange($gender = 'male')
    {
        if ($gender === 'female' && ($this->normal_min_female || $this->normal_max_female)) {
            $min = $this->normal_min_female;
            $max = $this->normal_max_female;
        } elseif ($gender === 'male' && ($this->normal_min_male || $this->normal_max_male)) {
            $min = $this->normal_min_male;
            $max = $this->normal_max_male;
        } else {
            $min = $this->normal_min;
            $max = $this->normal_max;
        }

        if ($min !== null && $max !== null) {
            return number_format($min, 2) . ' - ' . number_format($max, 2);
        } elseif ($min !== null) {
            return '>= ' . number_format($min, 2);
        } elseif ($max !== null) {
            return '<= ' . number_format($max, 2);
        }
        return '-';
    }

    public function checkFlag($value, $gender = 'male')
    {
        if (!is_numeric($value)) return null;
        $val = (float) $value;

        // Check critical ranges first
        if ($this->critical_low !== null && $val < $this->critical_low) return 'critical_low';
        if ($this->critical_high !== null && $val > $this->critical_high) return 'critical_high';

        // Get normal range based on gender
        if ($gender === 'female' && ($this->normal_min_female !== null || $this->normal_max_female !== null)) {
            $min = $this->normal_min_female;
            $max = $this->normal_max_female;
        } elseif ($gender === 'male' && ($this->normal_min_male !== null || $this->normal_max_male !== null)) {
            $min = $this->normal_min_male;
            $max = $this->normal_max_male;
        } else {
            $min = $this->normal_min;
            $max = $this->normal_max;
        }

        if ($min !== null && $val < $min) return 'low';
        if ($max !== null && $val > $max) return 'high';
        return 'normal';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('group_name')->orderBy('sort_order');
    }
}
