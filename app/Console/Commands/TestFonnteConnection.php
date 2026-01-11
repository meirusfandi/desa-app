<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;

class TestFonnteConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:test {target : The phone number to send to (e.g. 08123456789)} {message? : The message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a WhatsApp message via Fonnte Service';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnteService)
    {
        $target = $this->argument('target');
        $message = $this->argument('message') ?? 'Halo, ini adalah pesan tes dari Desa App via Fonnte/WhatsApp.';

        $this->info("Sending message to: {$target}");
        $this->info("Message: {$message}");

        $response = $fonnteService->send($target, $message);

        if ($response) {
            $this->info('Response received:');
            $this->table(['Key', 'Value'], collect($response)->map(function ($value, $key) {
                return [$key, is_array($value) ? json_encode($value) : $value];
            }));
            
            if (isset($response['status']) && $response['status']) {
                 $this->info('Message sent successfully!');
            } else {
                 $this->error('Fonnte returned failure status.');
                 
                 $reason = $response['reason'] ?? '';
                 if ($reason === 'request invalid on disconnected device') {
                     $this->warn("\nACTION REQUIRED: Your WhatsApp account is not connected to Fonnte.");
                     $this->warn("Please go to https://fonnte.com/dashboard and scan the QR code with your device.");
                 }
            }
        } else {
            $this->error('Failed to send message. Check logs or API token.');
        }
    }
}
