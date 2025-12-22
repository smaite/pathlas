<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'lab_id',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function bookingsCreated(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    public function resultsEntered(): HasMany
    {
        return $this->hasMany(Result::class, 'entered_by');
    }

    public function resultsApproved(): HasMany
    {
        return $this->hasMany(Result::class, 'approved_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin' || $this->isSuperAdmin();
    }

    public function isReceptionist(): bool
    {
        return $this->role?->name === 'receptionist';
    }

    public function isTechnician(): bool
    {
        return $this->role?->name === 'technician';
    }

    public function isPathologist(): bool
    {
        return $this->role?->name === 'pathologist';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role?->name, $roles);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Check if user's lab requires approval for results
    public function requiresApproval(): bool
    {
        return $this->lab?->require_approval ?? false;
    }
}
