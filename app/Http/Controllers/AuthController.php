<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginActivity;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.',
                ]);
            }

            $request->session()->regenerate();
            
            // Log login activity
            LoginActivity::logLogin($user, $request, 'login');
            \App\Models\ActivityLog::log('user_login', $user);

            return redirect()->intended(route('dashboard'));
        }

        // Log failed login attempt
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            LoginActivity::logLogin($user, $request, 'failed_login');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity
        LoginActivity::logLogin($user, $request, 'logout');
        \App\Models\ActivityLog::log('user_logout', $user);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
