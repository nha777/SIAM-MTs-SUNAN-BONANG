<?php

namespace App\Modules\Base\Events;

use App\Modules\Base\Enums\AuditEvent;
use Illuminate\Database\Eloquent\Model;

class ModelMutatedEvent extends BaseAuditEvent
{
    public AuditEvent $eventName;
    public string $auditableType;
    public int $auditableId;
    public ?array $oldValues;
    public ?array $newValues;
    public string $severity;

    public function __construct(
        AuditEvent $eventName,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'info'
    ) {
        parent::__construct();
        
        $this->eventName = $eventName;
        $this->auditableType = get_class($model);
        $this->auditableId = $model->getKey();
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->severity = $severity;
    }
}
