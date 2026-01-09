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
        $gender = strtolower(trim($gender ?? ''));
        
        $min = null;
        $max = null;

        if (($gender === 'female' || $gender === 'f') && ($this->normal_min_female || $this->normal_max_female)) {
            $min = $this->normal_min_female;
            $max = $this->normal_max_female;
        } elseif (($gender === 'male' || $gender === 'm') && ($this->normal_min_male || $this->normal_max_male)) {
            $min = $this->normal_min_male;
            $max = $this->normal_max_male;
        } else {
            // Try generic first
            $min = $this->normal_min;
            $max = $this->normal_max;

            // Fallback to male if generic is empty
            if ($min === null && $max === null) {
                if ($this->normal_min_male || $this->normal_max_male) {
                    $min = $this->normal_min_male;
                    $max = $this->normal_max_male;
                } elseif ($this->normal_min_female || $this->normal_max_female) {
                    $min = $this->normal_min_female;
                    $max = $this->normal_max_female;
                }
            }
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
        $gender = strtolower(trim($gender ?? ''));

        // Check critical ranges first
        if ($this->critical_low !== null && $val < $this->critical_low) return 'critical_low';
        if ($this->critical_high !== null && $val > $this->critical_high) return 'critical_high';

        $min = null;
        $max = null;

        // Get normal range based on gender
        if (($gender === 'female' || $gender === 'f') && ($this->normal_min_female !== null || $this->normal_max_female !== null)) {
            $min = $this->normal_min_female;
            $max = $this->normal_max_female;
        } elseif (($gender === 'male' || $gender === 'm') && ($this->normal_min_male !== null || $this->normal_max_male !== null)) {
            $min = $this->normal_min_male;
            $max = $this->normal_max_male;
        } else {
            $min = $this->normal_min;
            $max = $this->normal_max;

            // Fallback
            if ($min === null && $max === null) {
                if ($this->normal_min_male !== null || $this->normal_max_male !== null) {
                    $min = $this->normal_min_male;
                    $max = $this->normal_max_male;
                } elseif ($this->normal_min_female !== null || $this->normal_max_female !== null) {
                    $min = $this->normal_min_female;
                    $max = $this->normal_max_female;
                }
            }
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
