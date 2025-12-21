<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSuratRequestSubmittedToSekretaris extends Notification
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

        return (new MailMessage)
            ->subject("Pengajuan Surat Baru: {$title} (#{$surat->id})")
            ->greeting('Halo Sekretaris,')
            ->line('Ada pengajuan surat baru dari warga dan perlu ditinjau.')
            ->line('Nama Warga: ' . ($surat->user?->name ?? '-'))
            ->line('Jenis Surat: ' . $title)
            ->action('Lihat Pengajuan', route('sekretaris.approval.show', $surat))
            ->line('Terima kasih.');
    }
}
