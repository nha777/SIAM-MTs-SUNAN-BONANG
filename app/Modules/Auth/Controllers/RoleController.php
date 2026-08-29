<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use BaseApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('role.view');
        
        $roles = Role::withCount('users', 'permissions')->get();

        if ($request->wantsJson()) {
            return $this->successResponse($roles, 'Roles retrieved successfully');
        }

        return view('auth::roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('role.create');
        $permissions = Permission::orderBy('name')->get()->groupBy(function($data) {
            return explode('.', $data->name)[0];
        });
        return view('auth::roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('role.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        
        if ($request->wantsJson()) {
            return $this->successResponse($role, 'Role created successfully', 201);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Request $request, $id)
    {
        Gate::authorize('role.view');
        
        $role = Role::with('permissions')->findOrFail($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse($role, 'Role retrieved successfully');
        }

        return view('auth::roles.show', compact('role'));
    }

    public function edit($id)
    {
        Gate::authorize('role.update');
        
        $role = Role::findOrFail($id);
        
        // Protect super admin role
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $permissions = Permission::orderBy('name')->get()->groupBy(function($data) {
            return explode('.', $data->name)[0];
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('auth::roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('role.update');

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            if ($request->wantsJson()) return $this->errorResponse('Cannot modify Super Admin role.', 400);
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        $role->update(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }
        
        if ($request->wantsJson()) {
            return $this->successResponse($role, 'Role updated successfully');
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        Gate::authorize('role.delete');
        
        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            if ($request->wantsJson()) return $this->errorResponse('Cannot delete Super Admin role.', 400);
            return redirect()->route('roles.index')->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
             if ($request->wantsJson()) return $this->errorResponse('Cannot delete role that is in use.', 400);
             return redirect()->route('roles.index')->with('error', 'Role tidak dapat dihapus karena sedang digunakan oleh pengguna.');
        }

        $role->delete();
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Role deleted successfully');
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
