<?php

use App\Modules\Academic\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['subject.view', 'subject.create', 'subject.update', 'subject.delete']);
    $this->actingAs($this->user);
});

it('can list subjects', function () {
    $response = $this->get(route('subjects.index'));
    $response->assertStatus(200);
});

it('can store a new subject', function () {
    $data = [
        'code' => 'MAT-101',
        'name' => 'Matematika Dasar',
        'type' => 'Umum',
        'credits' => 4,
        'is_active' => true
    ];

    $response = $this->post(route('subjects.store'), $data);
    $response->assertRedirect(route('subjects.index'));
    
    $this->assertDatabaseHas('subjects', [
        'code' => 'MAT-101',
        'name' => 'Matematika Dasar'
    ]);
});

it('can update a subject', function () {
    $subject = Subject::create([
        'code' => 'FIS-101',
        'name' => 'Fisika Dasar',
        'type' => 'Peminatan',
        'credits' => 3
    ]);

    $data = [
        'code' => 'FIS-101',
        'name' => 'Fisika Lanjut',
        'type' => 'Peminatan',
        'credits' => 4,
        'is_active' => true
    ];

    $response = $this->put(route('subjects.update', $subject->id), $data);
    $response->assertRedirect(route('subjects.index'));
    
    $this->assertDatabaseHas('subjects', [
        'id' => $subject->id,
        'name' => 'Fisika Lanjut',
        'credits' => 4
    ]);
});

it('can delete a subject', function () {
    $subject = Subject::create([
        'code' => 'KIM-101',
        'name' => 'Kimia',
        'type' => 'Peminatan',
        'credits' => 3
    ]);

    $response = $this->delete(route('subjects.destroy', $subject->id));
    $response->assertRedirect(route('subjects.index'));
    
    $this->assertSoftDeleted('subjects', [
        'id' => $subject->id
    ]);
});
