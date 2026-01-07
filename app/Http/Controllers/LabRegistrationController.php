<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterLabRequest;
use App\Models\Lab;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LabRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-lab');
    }

    public function register(RegisterLabRequest $request)
    {
        $validated = $request->validated();

        // Generate unique lab code
        $labCode = strtoupper(Str::slug(substr($validated['lab_name'], 0, 3))) . '-' . strtoupper(Str::random(4));
        
        // Create Lab
        $lab = Lab::create([
            'name' => $validated['lab_name'],
            'code' => $labCode,
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'subscription_plan' => 'free_trial',
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addDays(14), // 14 day trial
            'is_verified' => false,
            'is_active' => true,
        ]);

        // Create Admin User for this Lab
        $adminRole = Role::where('name', 'admin')->first();
        
        $user = User::create([
            'name' => $validated['owner_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'role_id' => $adminRole->id,
            'lab_id' => $lab->id,
            'status' => 'active',
        ]);

        // Log in the user
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Lab registered successfully! Your 14-day free trial has started. Awaiting verification from super admin.');
    }
}
