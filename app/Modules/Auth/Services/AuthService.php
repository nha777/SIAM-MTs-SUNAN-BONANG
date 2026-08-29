<?php

namespace App\Modules\Auth\Services;

use App\Modules\Base\Services\BaseService;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService extends BaseService implements AuthServiceInterface
{
    /**
     * UserRepository instance.
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * AuthService constructor.
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    /**
     * Proses autentikasi pengguna berdasarkan email dan password.
     */
    public function login(string $email, string $password, bool $remember = false): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Percobaan masuk gagal untuk email: ' . $email, [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Lakukan login menggunakan Session Guard bawaan Laravel
        Auth::login($user, $remember);

        // Catat log audit login berhasil (manual karena login bukan mutasi Eloquent)
        $user->recordAuditLog('login_success', null, [
            'logged_in_at' => now()->toDateTimeString()
        ], 'info');

        Log::info('Pengguna berhasil masuk: ' . $user->email, [
            'user_id' => $user->id,
            'ip' => request()->ip()
        ]);

        return true;
    }

    /**
     * Keluar dari sesi aplikasi.
     */
    public function logout(): void
    {
        $user = Auth::user();

        if ($user) {
            $user->recordAuditLog('logout_success', null, [
                'logged_out_at' => now()->toDateTimeString()
            ], 'info');

            Log::info('Pengguna keluar sesi: ' . $user->email, [
                'user_id' => $user->id
            ]);
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
