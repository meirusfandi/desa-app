<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->paginate(10);
        return view('admin.role.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'role_name' => ['required', 'string', 'max:255'],
        ]);

        Role::create([
            'name' => $request->name,
            'role_name' => $request->role_name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.master.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'role_name' => ['required', 'string', 'max:255'],
        ]);

        $role->update([
            'name' => $request->name,
            'role_name' => $request->role_name,
        ]);

        return redirect()->route('admin.master.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deletion of critical roles if needed, e.g. admin
        if ($role->name === 'admin') {
             return back()->with('error', 'Cannot delete admin role.');
        }

        $role->delete();

        return redirect()->route('admin.master.roles.index')->with('success', 'Role deleted successfully.');
    }
}
