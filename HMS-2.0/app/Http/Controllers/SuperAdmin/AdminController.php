<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('business')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            });

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('business', function ($businessQuery) use ($search) {
                        $businessQuery->where('hospital_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $admins = $query->latest()->paginate(10);

        return view('super-admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $businesses = Business::where('is_active', true)
            ->orderBy('hospital_name')
            ->get();

        return view('super-admin.admins.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'business_id' => 'required|exists:businesses,id',
            'send_welcome_email' => 'boolean'
        ]);

        // Create the admin user
        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'business_id' => $validated['business_id'],
            'email_verified_at' => now(), // Auto-verify admin emails
        ]);

        // Assign admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        // Send welcome email if requested
        if ($request->boolean('send_welcome_email')) {
            // You can implement welcome email logic here
            // Mail::to($admin->email)->send(new WelcomeAdminMail($admin, $validated['password']));
        }

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', "Admin '{$admin->name}' created successfully.");
    }

    public function show(User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        $admin->load('business');

        return view('super-admin.admins.show', compact('admin'));
    }

    public function edit(User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        $businesses = Business::where('is_active', true)
            ->orderBy('hospital_name')
            ->get();

        return view('super-admin.admins.edit', compact('admin', 'businesses'));
    }

    public function update(Request $request, User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'business_id' => 'required|exists:businesses,id',
        ]);

        // Update admin details
        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'business_id' => $validated['business_id'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $admin->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', "Admin '{$admin->name}' updated successfully.");
    }

    public function destroy(User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        // Prevent deletion of the last admin
        $adminCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->count();

        if ($adminCount <= 1) {
            return redirect()
                ->route('super-admin.admins.index')
                ->with('error', 'Cannot delete the last admin user.');
        }

        // Store admin name for success message
        $adminName = $admin->name;

        // Remove roles and delete admin
        $admin->roles()->detach();
        $admin->delete();

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', "Admin '{$adminName}' deleted successfully.");
    }

    /**
     * Search admins via AJAX
     */
    public function search(Request $request)
    {
        $search = $request->get('q');

        $admins = User::with('business')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('business', function ($businessQuery) use ($search) {
                        $businessQuery->where('hospital_name', 'LIKE', "%{$search}%");
                    });
            })
            ->limit(10)
            ->get();

        return response()->json($admins);
    }

    /**
     * Toggle admin status
     */
    public function toggleStatus(User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        // Toggle email verification status as a way to enable/disable
        if ($admin->email_verified_at) {
            $admin->update(['email_verified_at' => null]);
            $status = 'disabled';
        } else {
            $admin->update(['email_verified_at' => now()]);
            $status = 'enabled';
        }

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', "Admin '{$admin->name}' has been {$status}.");
    }

    /**
     * Resend verification email
     */
    public function resendVerification(User $admin)
    {
        // Ensure this user is an admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }

        if ($admin->email_verified_at) {
            return redirect()
                ->route('super-admin.admins.index')
                ->with('error', 'Admin email is already verified.');
        }

        // Send verification email logic here
        // $admin->sendEmailVerificationNotification();

        return redirect()
            ->route('super-admin.admins.index')
            ->with('success', "Verification email sent to {$admin->email}.");
    }
}
