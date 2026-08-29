<?php

namespace App\Modules\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    /**
     * Jumlah maksimal percobaan job jika gagal.
     */
    public int $tries = 3;

    /**
     * Waktu tunggu (detik) antar percobaan.
     */
    public array $backoff = [10, 30, 60];

    /**
     * Batas waktu eksekusi job (detik).
     */
    public int $timeout = 30;

    /**
     * Gagal jika timeout.
     */
    public bool $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $metadata = [
            'actor_snapshot' => $this->payload['actor_snapshot'] ?? new \stdClass(),
            'roles' => $this->payload['actor_snapshot']['roles'] ?? [],
            'request_context' => [
                'request_id' => $this->payload['request_id'] ?? null,
                'ip_address' => $this->payload['ip_address'] ?? null,
                'user_agent' => $this->payload['user_agent'] ?? null,
            ],
            'extra' => new \stdClass(),
            'captured_at' => now()->toIso8601String(),
        ];

        DB::table('audit_logs')->insert([
            'event_id' => (string) Str::uuid(),
            'request_id' => $this->payload['request_id'],
            'severity' => $this->payload['severity'] ?? 'info',
            'actor_id' => $this->payload['actor_id'],
            'actor_type' => $this->payload['actor_type'],
            'event_name' => $this->payload['event_name'],
            'auditable_type' => $this->payload['auditable_type'],
            'auditable_id' => $this->payload['auditable_id'],
            'old_values' => $this->payload['old_values'] ? json_encode($this->payload['old_values']) : null,
            'new_values' => $this->payload['new_values'] ? json_encode($this->payload['new_values']) : null,
            'metadata' => json_encode($metadata),
            'ip_address' => $this->payload['ip_address'],
            'user_agent' => $this->payload['user_agent'],
            'created_at' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Simpan log error secara lokal agar tetap ada jejak saat database down
        \Illuminate\Support\Facades\Log::critical('Audit Log Failed to Save', [
            'error' => $exception->getMessage(),
            'payload' => $this->payload
        ]);
    }
}
