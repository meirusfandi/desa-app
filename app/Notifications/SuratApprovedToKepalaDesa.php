<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuratApprovedToKepalaDesa extends Notification
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
        $surat->loadMissing(['user', 'suratType']);

        $title = $surat->suratType?->name ?? 'Surat';

        $mail = (new MailMessage)
            ->subject("Perlu TTD Kepala Desa: {$title} (#{$surat->id})")
            ->greeting('Halo Kepala Desa,')
            ->line('Ada surat yang sudah disetujui sekretaris dan membutuhkan tanda tangan.')
            ->line('Nama Warga: ' . ($surat->user?->name ?? '-'))
            ->line('Jenis Surat: ' . $title);

        if (!empty($surat->notes)) {
            $mail->line('Catatan Sekretaris: ' . $surat->notes);
        }

        return $mail
            ->action('Buka untuk Ditandatangani', route('kepala.surat.show', $surat))
            ->line('Terima kasih.');
    }
}
