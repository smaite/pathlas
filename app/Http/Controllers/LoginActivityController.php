<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use Illuminate\Http\Request;

class LoginActivityController extends Controller
{
    public function index()
    {
        $activities = LoginActivity::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(20);

        return view('profile.login-activity', compact('activities'));
    }

    public function clearAll()
    {
        // Keep only the current session's login
        LoginActivity::where('user_id', auth()->id())
            ->where('activity_type', '!=', 'login')
            ->delete();

        // Keep only the last login record
        $lastLogin = LoginActivity::where('user_id', auth()->id())
            ->where('activity_type', 'login')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastLogin) {
            LoginActivity::where('user_id', auth()->id())
                ->where('id', '!=', $lastLogin->id)
                ->delete();
        }

        return back()->with('success', 'Login history cleared.');
    }
}
