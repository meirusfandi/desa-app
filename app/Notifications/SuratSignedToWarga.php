<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class SuratSignedToWarga extends Notification
{
    use Queueable;

    public function __construct(private readonly SuratRequest $surat)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $surat = $this->surat;
        $surat->loadMissing(['suratType']);

        $title = $surat->suratType?->name ?? 'Surat';

        $mail = (new MailMessage)
            ->subject("Surat Sudah Ditandatangani: {$title} (#{$surat->id})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga') . '.')
            ->line('Pengajuan surat Anda sudah ditandatangani oleh Kepala Desa.')
            ->line('Silakan ambil di kantor desa atau unduh versi PDF jika tersedia.');

        if (!empty($surat->signed_file) && Storage::disk('public')->exists($surat->signed_file)) {
            $mail->action('Unduh PDF', url(Storage::url($surat->signed_file)));
        } else {
            $mail->action('Lihat Status Pengajuan', route('warga.surat.index'));
        }

        return $mail->line('Terima kasih.');
    }
}
