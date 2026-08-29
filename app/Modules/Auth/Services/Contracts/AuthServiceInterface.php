<?php

namespace App\Modules\Auth\Services\Contracts;

use App\Modules\Base\Contracts\BaseServiceInterface;

interface AuthServiceInterface extends BaseServiceInterface
{
    /**
     * Proses autentikasi pengguna berdasarkan email dan password.
     */
    public function login(string $email, string $password, bool $remember = false): bool;

    /**
     * Keluar dari sesi aplikasi.
     */
    public function logout(): void;
}
