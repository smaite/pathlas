<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoginActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'activity_type',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a login activity
     */
    public static function logLogin(User $user, Request $request, string $type = 'login'): self
    {
        $userAgent = $request->userAgent();
        $parsed = self::parseUserAgent($userAgent);

        return self::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr($userAgent, 0, 255),
            'device_type' => $parsed['device'],
            'browser' => $parsed['browser'],
            'platform' => $parsed['platform'],
            'activity_type' => $type,
            'created_at' => now(),
        ]);
    }

    /**
     * Parse user agent to extract browser, platform, device
     */
    protected static function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return ['browser' => 'Unknown', 'platform' => 'Unknown', 'device' => 'Unknown'];
        }

        // Detect platform/OS
        $platform = 'Unknown';
        if (preg_match('/Windows NT 10.0/i', $userAgent)) $platform = 'Windows 10/11';
        elseif (preg_match('/Windows NT 6.3/i', $userAgent)) $platform = 'Windows 8.1';
        elseif (preg_match('/Windows NT 6.2/i', $userAgent)) $platform = 'Windows 8';
        elseif (preg_match('/Windows NT 6.1/i', $userAgent)) $platform = 'Windows 7';
        elseif (preg_match('/Mac OS X/i', $userAgent)) $platform = 'macOS';
        elseif (preg_match('/Android/i', $userAgent)) $platform = 'Android';
        elseif (preg_match('/iPhone|iPad/i', $userAgent)) $platform = 'iOS';
        elseif (preg_match('/Linux/i', $userAgent)) $platform = 'Linux';
        elseif (preg_match('/Ubuntu/i', $userAgent)) $platform = 'Ubuntu';
        elseif (preg_match('/Chrome OS/i', $userAgent)) $platform = 'Chrome OS';

        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/Edg\//i', $userAgent)) $browser = 'Microsoft Edge';
        elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/Opera|OPR/i', $userAgent)) $browser = 'Opera';
        elseif (preg_match('/MSIE|Trident/i', $userAgent)) $browser = 'Internet Explorer';

        // Detect device type
        $device = 'Desktop';
        if (preg_match('/Mobile|Android.*Mobile|iPhone/i', $userAgent)) $device = 'Mobile';
        elseif (preg_match('/Tablet|iPad/i', $userAgent)) $device = 'Tablet';

        return compact('browser', 'platform', 'device');
    }

    /**
     * Get icon for device type
     */
    public function getDeviceIconAttribute(): string
    {
        return match($this->device_type) {
            'Mobile' => '📱',
            'Tablet' => '📲',
            default => '💻',
        };
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get badge color based on activity type
     */
    public function getActivityBadgeAttribute(): string
    {
        return match($this->activity_type) {
            'login' => 'bg-green-100 text-green-700',
            'logout' => 'bg-gray-100 text-gray-700',
            'failed_login' => 'bg-red-100 text-red-700',
            default => 'bg-blue-100 text-blue-700',
        };
    }
}
