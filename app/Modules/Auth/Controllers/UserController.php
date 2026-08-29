<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\StoreUserRequest;
use App\Modules\Auth\Requests\UpdateUserRequest;
use App\Modules\Auth\Services\Contracts\UserServiceInterface;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use BaseApiResponse;

    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);
        
        $status = $request->input('status');
        $search = $request->input('search');

        $query = User::with('roles');
        
        if ($status === 'all') {
            $query->withTrashed();
        } elseif ($status === 'deleted') {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse($users, 'Users retrieved successfully');
        }

        return view('auth::users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        $roles = Role::orderBy('name')->get();
        return view('auth::users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);
        
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        
        $user = $this->userService->store($data);
        
        if ($request->wantsJson()) {
            return $this->successResponse($user, 'User created successfully', 201);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(Request $request, $id)
    {
        $user = $this->userService->getById($id, ['*'], ['roles']);
        
        if (!$user) {
            if ($request->wantsJson()) return $this->errorResponse('User not found', 404);
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        Gate::authorize('view', $user);

        if ($request->wantsJson()) {
            return $this->successResponse($user, 'User retrieved successfully');
        }

        return view('auth::users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->userService->getById($id, ['*'], ['roles']);
        
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        Gate::authorize('update', $user);
        
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view('auth::users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->getById($id);
        
        if (!$user) {
            if ($request->wantsJson()) return $this->errorResponse('User not found', 404);
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        Gate::authorize('update', $user);

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');
        
        $this->userService->update($id, $data);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'User updated successfully');
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->userService->getById($id);
        
        if (!$user) {
            if ($request->wantsJson()) return $this->errorResponse('User not found', 404);
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        Gate::authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            if ($request->wantsJson()) return $this->errorResponse('You cannot delete your own account.', 400);
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->userService->remove($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'User deleted successfully');
        }

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function restore(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $user);

        $this->userService->restore($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse(null, 'User restored successfully');
        }

        return redirect()->route('users.index')->with('success', 'User berhasil dipulihkan.');
    }
}
