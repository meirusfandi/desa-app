<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.fonnte.token') ?? '';
        $this->baseUrl = config('services.fonnte.base_url') ?? 'https://api.fonnte.com';
    }

    /**
     * Send WhatsApp message via Fonnte.
     *
     * @param string $target Phone number (e.g., '08123456789' or '628123456789')
     * @param string $message The message content
     * @return array|null Response data or null on failure
     */
    public function send(string $target, string $message): ?array
    {
        if (empty($this->token)) {
            Log::warning('Fonnte token is not configured. Message not sent to: ' . $target);
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post("{$this->baseUrl}/send", [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default country code for Indonesia if not present
            ]);

            if ($response->successful()) {
                Log::info('Fonnte message sent successfully to: ' . $target);
                return $response->json();
            } else {
                Log::error('Fonnte API Error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
