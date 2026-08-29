<?php

use App\Modules\Academic\Models\AcademicClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['academic.view', 'academic.create', 'academic.update', 'academic.delete']);
    $this->actingAs($this->user);
});

it('can list academic classes', function () {
    $response = $this->get(route('classes.index'));
    $response->assertStatus(200);
});

it('can store a new class', function () {
    $data = [
        'name' => 'XI-IPA-1',
        'level' => '11',
        'capacity' => 30,
        'is_active' => true
    ];

    $response = $this->post(route('classes.store'), $data);
    $response->assertRedirect(route('classes.index'));
    
    $this->assertDatabaseHas('classes', [
        'name' => 'XI-IPA-1'
    ]);
});
