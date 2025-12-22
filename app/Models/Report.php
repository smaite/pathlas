<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'booking_id',
        'pdf_path',
        'qr_code',
        'generated_by',
        'generated_at',
        'delivered_at',
        'delivery_method',
        'is_final',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_final' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($report) {
            if (empty($report->report_id)) {
                $report->report_id = 'RPT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_path) {
            return asset('storage/' . $this->pdf_path);
        }
        return null;
    }
}
