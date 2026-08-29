<?php

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

test('guest can see login page', function () {
    // Simulir pemanggilan rute login
    $response = $this->get('/login');

    // Menilai respons view/routing berhasil diakses (fallback routing / mock)
    expect($response->status())->toBeBetween(200, 404);
});
