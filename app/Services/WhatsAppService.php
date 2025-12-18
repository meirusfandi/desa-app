<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        $response = Http::withHeaders([
            'Authorization' => env('WA_TOKEN'),
        ])->post(env('WA_URL'), [
            'phone' => $phone,
            'message' => $message,
        ]);

        return $response->successful();
    }
}
