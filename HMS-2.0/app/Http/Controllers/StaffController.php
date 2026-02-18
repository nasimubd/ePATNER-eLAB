<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Business;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Staff::with(['business', 'user.roles']);

        // Filter by business if user is not super-admin
        if (!Auth::user()->roles->contains('name', 'super-admin')) {
            $query->where('business_id', Auth::user()->business_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by business
        if ($request->has('business_id') && $request->business_id) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $staff = $query->paginate(15);
        $businesses = Business::where('is_active', true)->get();

        return view('staff.index', compact('staff', 'businesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businesses = Business::where('is_active', true)->get();
        $roles = Role::whereIn('name', ['Manager', 'Doctor', 'LA'])->get();



        return view('staff.create', compact('businesses', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Default password
                'business_id' => $request->business_id,
            ]);

            // Assign role to user
            $user->assignRole($request->role);

            // Create staff record
            $staff = Staff::create([
                'business_id' => $request->business_id,
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_id' => $user->id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('staff.index')
                ->with('success', 'Staff member created successfully. Default password is: password123');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating staff member: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {


        $staff->load(['business', 'user.roles']);

        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {


        $businesses = Business::where('is_active', true)->get();
        $roles = Role::whereIn('name', ['Manager', 'Doctor', 'LA'])->get();
        $staff->load('user.roles');

        return view('staff.edit', compact('staff', 'businesses', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaffRequest $request, Staff $staff)
    {


        try {
            DB::beginTransaction();

            // Update user account
            if ($staff->user) {
                $staff->user->update([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'business_id' => $request->business_id,
                ]);

                // Update role
                $staff->user->syncRoles([$request->role]);
            }

            // Update staff record
            $staff->update([
                'business_id' => $request->business_id,
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('staff.index')
                ->with('success', 'Staff member updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating staff member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {

        try {
            DB::beginTransaction();

            // Delete associated user account
            if ($staff->user) {
                $staff->user->delete();
            }

            // Delete staff record
            $staff->delete();

            DB::commit();

            return redirect()->route('staff.index')
                ->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting staff member: ' . $e->getMessage());
        }
    }

    /**
     * Toggle staff status (active/inactive).
     */
    public function toggleStatus(Staff $staff)
    {

        $staff->update([
            'is_active' => !$staff->is_active
        ]);

        $status = $staff->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Staff member has been {$status} successfully.");
    }

    /**
     * Reset staff password.
     */
    public function resetPassword(Staff $staff)
    {

        if ($staff->user) {
            $newPassword = 'password123';
            $staff->user->update([
                'password' => Hash::make($newPassword)
            ]);

            return redirect()->back()
                ->with('success', "Password reset successfully. New password: {$newPassword}");
        }

        return redirect()->back()
            ->with('error', 'No user account found for this staff member.');
    }
}
