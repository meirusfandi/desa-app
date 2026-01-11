<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuratApprovedToWarga extends Notification
{
    use Queueable;

    public function __construct(private readonly SuratRequest $surat)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', \App\Channels\FonnteChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $surat = $this->surat;
        $title = $surat->suratType?->name ?? 'Surat';

        return (new MailMessage)
            ->subject("Surat Disetujui: {$title} (#{$surat->id})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga') . '.')
            ->line('Pengajuan surat Anda (' . $title . ') telah DISETUJUI oleh Admin/Sekretaris.')
            ->line('Surat sedang dalam proses penandatanganan oleh Kepala Desa.')
            ->action('Lihat Status', route('warga.surat.index'))
            ->line('Terima kasih.');
    }
}
