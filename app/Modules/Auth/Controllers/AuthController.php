<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use App\Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * AuthServiceInterface instance.
     */
    protected AuthServiceInterface $authService;

    /**
     * AuthController constructor.
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Menampilkan formulir masuk/login.
     */
    public function showLoginForm(): View
    {
        return view('auth::login');
    }

    /**
     * Memproses permintaan masuk.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $remember = $request->filled('remember');

        $this->authService->login($credentials['email'], $credentials['password'], $remember);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Memproses keluar dari aplikasi.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login');
    }
}
