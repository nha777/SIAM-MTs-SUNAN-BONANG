<?php

namespace App\Modules\Base\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseAuditEvent
{
    use Dispatchable, SerializesModels;

    public string $requestId;
    public string $ipAddress;
    public string $userAgent;
    public ?array $actorSnapshot = null;
    public ?int $actorId = null;
    public ?string $actorType = null;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->requestId = app()->has('request_id') ? app('request_id') : 'system-request';
        $this->ipAddress = Request::ip() ?? '127.0.0.1';
        $this->userAgent = Request::userAgent() ?? 'System/Console';
        
        $this->captureActorContext();
    }

    protected function captureActorContext(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->actorId = $user->getAuthIdentifier();
            $this->actorType = get_class($user);
            
            $roles = [];
            if (method_exists($user, 'getRoleNames')) {
                $roles = $user->getRoleNames()->toArray();
            }
            
            $this->actorSnapshot = [
                'name' => $user->name ?? 'Unknown',
                'email' => $user->email ?? 'Unknown',
                'roles' => $roles,
            ];
        }
    }
}
