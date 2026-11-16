<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Branch, User, Role, SystemSetting, Company, Institute};
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $company = Company::with('institutes.branches')->first();
        $branches = Branch::all();
        $users = User::with(['role', 'branch'])->paginate(15);
        $roles = Role::all();
        $settings = SystemSetting::all()->pluck('value', 'key');

        return view('settings.index', compact('company', 'branches', 'users', 'roles', 'settings'));
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:branches,code',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
        ]);

        $validated['status'] = 'active';
        Branch::create($validated);

        return back()->with('success', 'New branch added successfully.');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'designation' => 'nullable|string|max:100',
            'password' => 'required|min:6',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $branch = Branch::findOrFail($validated['branch_id']);

        User::create([
            'company_id' => $branch->institute->company_id ?? 1,
            'branch_id' => $branch->id,
            'role_id' => $role->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'user_type' => $role->slug,
            'designation' => $validated['designation'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'User account created successfully.');
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        foreach ($data as $key => $val) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'group' => 'general']
            );
        }

        return back()->with('success', 'System settings updated.');
    }
}
