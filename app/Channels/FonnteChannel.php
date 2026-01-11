<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FonnteService;

class FonnteChannel
{
    public function __construct(protected FonnteService $fonnteService)
    {
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFonnte')) {
            return;
        }

        $message = $notification->toFonnte($notifiable);
        
        // Ensure we have a valid target (phone number)
        $target = $notifiable->mobile_phone ?? $notifiable->phone ?? null;
        
        if (empty($target)) {
            return;
        }

        $this->fonnteService->send($target, $message);
    }
}
