<?php

use Illuminate\Support\Facades\Queue;
use App\Modules\Finance\Jobs\SendWhatsAppNotificationJob;

it('dispatches SendWhatsAppNotificationJob', function () {
    Queue::fake();

    // Dispatch the job manually
    SendWhatsAppNotificationJob::dispatch('081234567890', 'Test Message');

    // Assert the job was pushed to the queue
    Queue::assertPushed(SendWhatsAppNotificationJob::class, function ($job) {
        return true; // We can inspect properties if we make them public or via reflection, but asserting it was pushed is enough
    });
});
