<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Super admin bypasses subscription check
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has a lab
        if ($user && $user->lab) {
            $lab = $user->lab;

            // Check if lab is verified
            if (!$lab->is_verified) {
                return redirect()->route('subscription.pending')
                    ->with('warning', 'Your lab is pending verification. Please wait for admin approval.');
            }

            // Check if subscription is active
            if (!$lab->isSubscriptionActive()) {
                return redirect()->route('subscription.expired')
                    ->with('error', 'Your subscription has expired. Please renew to continue.');
            }
        }

        return $next($request);
    }
}
