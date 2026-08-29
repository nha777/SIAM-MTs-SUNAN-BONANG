<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Modules\Auth\Services\Contracts\UserServiceInterface;
use App\Modules\Base\Traits\BaseApiResponse;

class ProfileController extends Controller
{
    use BaseApiResponse;

    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function show(Request $request)
    {
        $user = auth()->user();
        
        if ($request->wantsJson()) {
            return $this->successResponse($user, 'Profile retrieved successfully');
        }

        return view('auth::profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $this->userService->update($user->id, $validated);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Profile updated successfully');
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->userService->update($user->id, ['password' => $validated['password']]);

        if ($request->wantsJson()) {
            return $this->successResponse(null, 'Password updated successfully');
        }

        return redirect()->route('profile.show')->with('success', 'Password berhasil diperbarui.');
    }
}
