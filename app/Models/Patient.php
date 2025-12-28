<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'age',
        'gender',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'blood_group',
        'medical_history',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($patient) {
            if (empty($patient->patient_id)) {
                // Get the last patient_id number
                $lastPatient = Patient::orderByRaw('CAST(SUBSTRING(patient_id, 5) AS UNSIGNED) DESC')->first();
                if ($lastPatient && preg_match('/PAT-(\d+)/', $lastPatient->patient_id, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }
                $patient->patient_id = 'PAT-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get age - calculate from DOB if age is null
     */
    public function getAgeAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }
        
        return null;
    }

    public function getFullDetailsAttribute(): string
    {
        $age = $this->age ?? '-';
        $gender = $this->gender ? strtoupper(substr($this->gender, 0, 1)) : '-';
        $phone = $this->phone ?? 'No phone';
        return "{$this->name} ({$age} {$gender}) - {$phone}";
    }
}
