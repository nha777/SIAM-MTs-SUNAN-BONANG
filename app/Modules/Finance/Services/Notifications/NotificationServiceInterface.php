<?php
namespace App\Modules\Finance\Services\Notifications;

interface NotificationServiceInterface
{
    public function sendWhatsApp(string $phoneNumber, string $message): bool;
}
