<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // Get lab-scoped user query (lab admin only sees their lab's users)
    private function labQuery()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return User::query();
        }
        
        // Lab admins see users in their lab
        return User::where('lab_id', $user->lab_id);
    }

    public function index(Request $request)
    {
        $query = $this->labQuery()->with(['role', 'lab'])->latest();

        // Exclude superadmin role from display for non-superadmins
        if (!auth()->user()->isSuperAdmin()) {
            $query->whereHas('role', function($q) {
                $q->where('name', '!=', 'superadmin');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $roles = Role::where('name', '!=', 'superadmin')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        // Assign user to same lab as current user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $validated['role_id'],
            'lab_id' => auth()->user()->lab_id,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        ActivityLog::log('user_created', $user, [], ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeLabAccess($user);
        
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeLabAccess($user);

        $validated = $request->validated();

        $oldValues = $user->only(['name', 'email', 'role_id', 'status']);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }

        ActivityLog::log('user_updated', $user, $oldValues, $validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeLabAccess($user);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        ActivityLog::log('user_deleted', $user, $user->toArray());
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeLabAccess($user);
        
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::log('user_status_changed', $user, 
            ['status' => $user->getOriginal('status')], 
            ['status' => $newStatus]
        );

        return back()->with('success', "User status changed to {$newStatus}.");
    }

    public function activityLogs(Request $request)
    {
        $user = auth()->user();
        $query = ActivityLog::with('user')->latest();

        // Lab-scope activity logs
        if (!$user->isSuperAdmin()) {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('lab_id', $user->lab_id);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(50);
        
        // Get users from same lab for filter dropdown
        $users = $this->labQuery()->select('id', 'name')->get();

        return view('users.activity-logs', compact('logs', 'users'));
    }

    private function authorizeLabAccess($user)
    {
        $currentUser = auth()->user();
        
        // Super admin can access all
        if ($currentUser->isSuperAdmin()) {
            return;
        }
        
        // If user has no lab (legacy data), allow access
        if ($user->lab_id === null) {
            return;
        }
        
        // Others can only access their lab's users
        if ($user->lab_id !== $currentUser->lab_id) {
            abort(403, 'Unauthorized access to user.');
        }
    }
}
