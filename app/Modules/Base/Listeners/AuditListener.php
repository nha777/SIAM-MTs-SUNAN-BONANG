<?php

namespace App\Modules\Base\Listeners;

use App\Modules\Base\Events\BaseAuditEvent;
use App\Modules\Base\Events\ModelMutatedEvent;
use App\Modules\Base\Jobs\StoreAuditLogJob;

class AuditListener
{
    /**
     * Handle the event.
     */
    public function handle(BaseAuditEvent $event): void
    {
        // Extract payload
        $payload = [
            'request_id' => $event->requestId,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'actor_id' => $event->actorId,
            'actor_type' => $event->actorType,
            'actor_snapshot' => $event->actorSnapshot,
        ];

        if ($event instanceof ModelMutatedEvent) {
            $payload['event_name'] = $event->eventName->value;
            $payload['auditable_type'] = $event->auditableType;
            $payload['auditable_id'] = $event->auditableId;
            $payload['old_values'] = $event->oldValues;
            $payload['new_values'] = $event->newValues;
            $payload['severity'] = $event->severity;
            
            // Asynchronous Job Queue (Database / Redis)
            StoreAuditLogJob::dispatch($payload);
        }
    }
}
