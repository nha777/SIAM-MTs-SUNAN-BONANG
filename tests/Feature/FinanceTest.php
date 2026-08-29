<?php

use App\Modules\Finance\Models\Invoice;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = \App\Modules\Auth\Models\User::factory()->create();
    $this->user->givePermissionTo(['finance.view', 'finance.create', 'finance.update', 'finance.delete']);
    $this->actingAs($this->user);
});

it('can view finance dashboard (invoices list)', function () {
    $response = $this->get(route('invoices.index'));
    $response->assertStatus(200);
});
