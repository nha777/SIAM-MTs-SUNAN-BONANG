<?php

namespace App\Modules\Base\Traits;

use App\Modules\Base\Enums\AuditEvent;
use App\Modules\Base\Events\ModelMutatedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasAuditLogs
{
    /**
     * Boot the trait to listen to model events.
     */
    public static function bootHasAuditLogs(): void
    {
        static::created(function (Model $model) {
            $eventEnum = $model->mapToAuditEvent('created');
            if ($eventEnum) {
                ModelMutatedEvent::dispatch($eventEnum, $model, null, $model->getAttributes());
            }
        });

        static::updated(function (Model $model) {
            $oldValues = array_intersect_key($model->getRawOriginal(), $model->getDirty());
            $newValues = $model->getDirty();

            // Saring kolom sensitif seperti password
            $sensitiveColumns = ['password', 'remember_token', 'pin'];
            foreach ($sensitiveColumns as $col) {
                unset($oldValues[$col], $newValues[$col]);
            }

            if (!empty($newValues)) {
                $eventEnum = $model->mapToAuditEvent('updated');
                if ($eventEnum) {
                    ModelMutatedEvent::dispatch($eventEnum, $model, $oldValues, $newValues);
                }
            }
        });

        static::deleted(function (Model $model) {
            $eventEnum = $model->mapToAuditEvent('deleted');
            if ($eventEnum) {
                ModelMutatedEvent::dispatch($eventEnum, $model, $model->getRawOriginal(), null, 'warning');
            }
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $eventEnum = $model->mapToAuditEvent('restored');
                if ($eventEnum) {
                    ModelMutatedEvent::dispatch($eventEnum, $model, null, null, 'info');
                }
            });
        }
    }

    /**
     * Record an audit log entry manually for this model.
     */
    public function recordAuditLog(string $eventName, ?array $oldValues = null, ?array $newValues = null, string $severity = 'info'): void
    {
        $actor = auth()->user();
        $actorSnapshot = null;
        $actorId = null;
        $actorType = null;

        if ($actor) {
            $actorId = $actor->getAuthIdentifier();
            $actorType = get_class($actor);
            $roles = [];
            if (method_exists($actor, 'getRoleNames')) {
                $roles = $actor->getRoleNames()->toArray();
            }
            $actorSnapshot = [
                'name' => $actor->name ?? null,
                'email' => $actor->email ?? null,
                'roles' => $roles,
            ];
        } else {
            // when no authenticated actor, use this model as actor
            $actorId = $this->getKey();
            $actorType = get_class($this);
            $actorSnapshot = [
                'name' => $this->name ?? null,
                'email' => $this->email ?? null,
                'roles' => [],
            ];
        }

        $payload = [
            'event_name' => $eventName,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'severity' => $severity,
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'actor_snapshot' => $actorSnapshot,
            'ip_address' => request()?->ip() ?? null,
            'user_agent' => request()?->userAgent() ?? null,
            'request_id' => app()->has('request_id') ? app('request_id') : null,
        ];

        // Dispatch the job to store audit log; use sync queue if queue not configured
        try {
            \App\Modules\Base\Jobs\StoreAuditLogJob::dispatch($payload);
        } catch (\Throwable $e) {
            // Fallback: write to log so we don't break user flow
            \Illuminate\Support\Facades\Log::warning('Failed to dispatch audit log job: '.$e->getMessage(), ['payload' => $payload]);
        }
    }

    /**
     * Map model event to AuditEvent enum.
     */
    protected function mapToAuditEvent(string $event): ?AuditEvent
    {
        $className = class_basename(static::class);
        $prefix = Str::upper(Str::snake($className));
        $suffix = Str::upper($event);
        
        $enumName = $prefix . '_' . $suffix;
        
        foreach (AuditEvent::cases() as $case) {
            if ($case->name === $enumName) {
                return $case;
            }
        }
        
        return null;
    }
}
