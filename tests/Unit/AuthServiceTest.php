<?php

use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
    $this->authService = new AuthService($this->userRepository);
});

afterEach(function () {
    Mockery::close();
});

test('login fails with invalid credentials', function () {
    $this->userRepository->shouldReceive('findByEmail')
        ->once()
        ->with('test@siam.sch.id')
        ->andReturn(null);

    expect(fn() => $this->authService->login('test@siam.sch.id', 'password'))
        ->toThrow(ValidationException::class);
});

test('login succeeds with correct credentials', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->password = Hash::make('password123');
    $user->email = 'admin@siam.sch.id';
    $user->id = 1;

    $this->userRepository->shouldReceive('findByEmail')
        ->once()
        ->with('admin@siam.sch.id')
        ->andReturn($user);

    $user->shouldReceive('recordAuditLog')
        ->once()
        ->with('login_success', null, Mockery::any(), 'info');

    Auth::shouldReceive('login')
        ->once()
        ->with($user, false);

    $result = $this->authService->login('admin@siam.sch.id', 'password123');

    expect($result)->toBeTrue();
});
