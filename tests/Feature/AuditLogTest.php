<?php

namespace Tests\Feature;

use App\Modules\Auth\Models\User;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Modules\Base\Events\ModelMutatedEvent;
use App\Modules\Base\Jobs\StoreAuditLogJob;
use App\Modules\Base\Enums\AuditEvent;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_student_dispatches_audit_event()
    {
        Event::fake([ModelMutatedEvent::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::create([
            'name' => 'John Doe',
            'nisn' => '1234567890',
        ]);

        Event::assertDispatched(ModelMutatedEvent::class, function ($event) use ($student) {
            return $event->eventName === AuditEvent::STUDENT_CREATED
                && $event->auditableId === $student->id
                && $event->auditableType === Student::class;
        });
    }

    public function test_audit_event_is_listened_by_audit_listener()
    {
        Event::fake();
        Event::assertListening(
            ModelMutatedEvent::class,
            \App\Modules\Base\Listeners\AuditListener::class
        );
    }

    public function test_audit_listener_maps_payload_correctly()
    {
        Queue::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();
        
        $event = new ModelMutatedEvent(AuditEvent::STUDENT_CREATED, $student, null, $student->getAttributes());
        
        $listener = new \App\Modules\Base\Listeners\AuditListener();
        $listener->handle($event);

        Queue::assertPushed(StoreAuditLogJob::class, function ($job) use ($user, $student) {
            $payload = $job->payload;
            return $payload['event_name'] === AuditEvent::STUDENT_CREATED->value
                && $payload['actor_id'] === $user->id
                && $payload['auditable_type'] === Student::class
                && $payload['auditable_id'] === $student->id;
        });
    }

    public function test_audit_listener_pushes_job_to_queue()
    {
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();
        
        Queue::assertPushed(StoreAuditLogJob::class, function ($job) {
            return $job->payload['event_name'] === AuditEvent::STUDENT_CREATED->value;
        });
    }
    
    public function test_audit_logs_are_stored_in_database()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Make sure queue is sync for this test
        config(['queue.default' => 'sync']);

        $student = Student::factory()->create([
            'name' => 'John Doe',
            'nisn' => '1234567890'
        ]);
        
        $this->assertDatabaseHas('audit_logs', [
            'event_name' => AuditEvent::STUDENT_CREATED->value,
            'auditable_type' => Student::class,
            'auditable_id' => $student->id,
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);
    }


    public function test_audit_log_is_immutable()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        $log = \App\Modules\Base\Models\AuditLog::first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Audit Log Immutability Violation: Cannot update an audit log record.');
        $log->update(['severity' => 'critical']);
    }

    public function test_audit_log_cannot_be_deleted()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = Student::factory()->create();

        $log = \App\Modules\Base\Models\AuditLog::first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Audit Log Immutability Violation: Cannot delete an audit log record.');
        $log->delete();
    }

    public function test_audit_log_policy_denies_all_mutations()
    {
        $user = User::factory()->create();
        $log = new \App\Modules\Base\Models\AuditLog();

        $this->assertTrue($user->cannot('create', \App\Modules\Base\Models\AuditLog::class));
        $this->assertTrue($user->cannot('update', $log));
        $this->assertTrue($user->cannot('delete', $log));
        $this->assertTrue($user->cannot('forceDelete', $log));
    }

    public function test_metadata_snapshot_validation()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@test.com']);
        $this->actingAs($user);

        $student = Student::factory()->create();

        $log = \App\Modules\Base\Models\AuditLog::where('event_name', AuditEvent::STUDENT_CREATED->value)->first();

        $this->assertNotNull($log->metadata);
        
        $metadata = $log->metadata;
        
        $this->assertArrayHasKey('actor_snapshot', $metadata);
        $this->assertArrayHasKey('roles', $metadata);
        $this->assertArrayHasKey('request_context', $metadata);
        $this->assertArrayHasKey('extra', $metadata);
        $this->assertArrayHasKey('captured_at', $metadata);

        $this->assertEquals('Admin User', $metadata['actor_snapshot']['name']);
        $this->assertEquals('admin@test.com', $metadata['actor_snapshot']['email']);
    }

    public function test_request_correlation_end_to_end()
    {
        config(['queue.default' => 'sync']);
        // create a route to test the middleware
        \Illuminate\Support\Facades\Route::middleware(\App\Http\Middleware\RequestCorrelationMiddleware::class)->get('/test-correlation', function () {
            // trigger an event to create audit log
            $user = User::factory()->create();
            \Illuminate\Support\Facades\Auth::login($user);
            $student = Student::factory()->create();
            return response()->json(['success' => true]);
        });

        $response = $this->get('/test-correlation');
        
        $response->assertHeader('X-Request-ID');
        $requestId = $response->headers->get('X-Request-ID');

        $this->assertNotEmpty($requestId);

        $log = \App\Modules\Base\Models\AuditLog::latest('id')->first();
        
        $this->assertNotNull($log);
        $this->assertEquals($requestId, $log->request_id);
    }

    public function test_sensitive_field_filtering()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create();
        
        // This will create an event for User update
        $user->update([
            'password' => bcrypt('baru'),
            'remember_token' => 'new-token',
        ]);
        
        $log = \App\Modules\Base\Models\AuditLog::where('event_name', AuditEvent::USER_UPDATED->value)->latest()->first();
        
        $this->assertNotNull($log);
        
        $oldValues = $log->old_values;
        $newValues = $log->new_values;
        
        $this->assertArrayNotHasKey('password', $oldValues);
        $this->assertArrayNotHasKey('password', $newValues);
        $this->assertArrayNotHasKey('remember_token', $oldValues);
        $this->assertArrayNotHasKey('remember_token', $newValues);
        $this->assertArrayNotHasKey('pin', $oldValues ?? []);
        $this->assertArrayNotHasKey('pin', $newValues ?? []);
    }

}
