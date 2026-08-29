<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use App\Modules\Base\Traits\BaseApiResponse;

class PermissionController extends Controller
{
    use BaseApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('permission.view');
        
        $permissions = Permission::orderBy('name')->get()->groupBy(function($data) {
            return explode('.', $data->name)[0];
        });

        if ($request->wantsJson()) {
            return $this->successResponse($permissions, 'Permissions retrieved successfully');
        }

        return view('auth::permissions.index', compact('permissions'));
    }

    public function show(Request $request, $id)
    {
        Gate::authorize('permission.view');
        
        $permission = Permission::with('roles')->findOrFail($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse($permission, 'Permission retrieved successfully');
        }

        return view('auth::permissions.show', compact('permission'));
    }
}
