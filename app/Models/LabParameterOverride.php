<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabParameterOverride extends Model
{
    protected $fillable = [
        'lab_id',
        'test_parameter_id',
        'overrides',
        'is_active',
    ];

    protected $casts = [
        'overrides' => 'array',
        'is_active' => 'boolean',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function parameter()
    {
        return $this->belongsTo(TestParameter::class, 'test_parameter_id');
    }

    public function getOverride(string $key, $default = null)
    {
        return $this->overrides[$key] ?? $default;
    }
}
