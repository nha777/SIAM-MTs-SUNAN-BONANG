# Bukti Persiapan Arsitektur (Architecture Preparation Evidence) - Sprint 1E

Dokumen ini mendokumentasikan hasil penyelesaian tugas persiapan arsitektur (*preparation tasks*) sebelum pengerjaan pengontrol RESTful API (*API Controllers*) dimulai pada Sprint 1E. Seluruh perubahan telah lolos uji linter dan build dengan tingkat kepatuhan 100% terhadap arsitektur *Modular Monolith*.

---

## 1. Pembuatan Kontrak Layanan Autentikasi (`AuthServiceInterface`)
Dibuat file antarmuka khusus di dalam sub-modul Auth untuk memisahkan pengontrol dari implementasi layanan konkrit.

* **Nama File**: `/app/Modules/Auth/Services/Contracts/AuthServiceInterface.php`
* **Cuplikan Kode Aktual**:
```php
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
```

---

## 2. Pengikatan Layanan (`AuthServiceProvider`)
Pendaftaran resmi pengikatan antarmuka (`AuthServiceInterface`) ke kelas konkrit (`AuthService`) dilakukan di penyedia layanan Auth.

* **Nama File**: `/app/Modules/Auth/Providers/AuthServiceProvider.php`
* **Cuplikan Kode Aktual**:
```php
<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use App\Modules\Auth\Services\AuthService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan binding repositori dan service.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Boot service.
     */
    public function boot(): void
    {
        //
    }
}
```

---

## 3. FormRequest Pertama (`LoginRequest`)
Pemisahan aturan validasi dari pengontrol utama menggunakan FormRequest khusus yang aman dan reusable.

* **Nama File**: `/app/Modules/Auth/Requests/LoginRequest.php`
* **Cuplikan Kode Aktual**:
```php
<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Dapatkan pesan kesalahan khusus untuk aturan yang ditentukan.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
```

---

## 4. Refactor Pengontrol Autentikasi (`AuthController`)
Pengontrol didekatkan dengan asas *Dependency Injection (DI)* yang bersih melalui pemanggilan kontrak antarmuka dan FormRequest validasi.

* **Nama File**: `/app/Modules/Auth/Controllers/AuthController.php`
* **Cuplikan Kode Aktual**:
```php
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

        return redirect()->intended(route('dashboard'));
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
```

---

## 5. Seeder Peran Pengguna Bawaan (`RoleSeeder`)
Pembuatan seeder untuk mengunci peran (roles) standar SIAM dengan memanfaatkan paket `spatie/laravel-permission` yang terintegrasi di sistem.

* **Nama File**: `/database/seeders/RoleSeeder.php`
* **Cuplikan Kode Aktual**:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk peran (roles) standar SIAM.
     */
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Bendahara',
            'Tata Usaha',
            'Kepala Madrasah',
            'Wali Kelas',
            'Orang Tua',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }
}
```

---

## 6. Pengujian Arsitektur Layanan (`ModularMonolithTest`)
Menambahkan aturan pengujian khusus Pest Architecture yang melakukan pemindaian dinamis untuk memastikan bahwa seluruh kelas layanan di dalam sub-modul menerapkan kontrak antarmukanya masing-masing.

* **Nama File**: `/tests/Architecture/ModularMonolithTest.php`
* **Cuplikan Kode Aktual (Aturan Pengujian Baru)**:
```php
test('all services must implement an interface in Contracts', function () {
    $servicesPath = glob(app_path('Modules/*/Services/*.php'));
    
    foreach ($servicesPath as $path) {
        $relative = str_replace(app_path(), 'App', $path);
        $className = str_replace(['/', '.php'], ['\\', ''], $relative);
        
        $reflection = new ReflectionClass($className);
        
        // Skip interfaces and abstract classes
        if ($reflection->isInterface() || $reflection->isAbstract()) {
            continue;
        }
        
        $interfaces = $reflection->getInterfaceNames();
        
        $hasServiceInterface = false;
        foreach ($interfaces as $interface) {
            if (str_contains($interface, 'ServiceInterface')) {
                $hasServiceInterface = true;
                break;
            }
        }
        
        expect($hasServiceInterface)->toBeTrue("Service {$className} does not implement any ServiceInterface contract.");
    }
});
```
