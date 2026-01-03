<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestOverride extends Model
{
    protected $fillable = [
        'lab_id',
        'test_id',
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

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Get a specific override value or return null
     */
    public function getOverride(string $key, $default = null)
    {
        return $this->overrides[$key] ?? $default;
    }

    /**
     * Set a specific override value
     */
    public function setOverride(string $key, $value): self
    {
        $overrides = $this->overrides ?? [];
        $overrides[$key] = $value;
        $this->overrides = $overrides;
        return $this;
    }
}
