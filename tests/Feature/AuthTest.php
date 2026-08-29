<?php

use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

it('can login user', function () {
    $user = User::factory()->create([
        'email' => 'admin@siam.test',
        'password' => bcrypt('password123')
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@siam.test',
        'password' => 'password123'
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});
