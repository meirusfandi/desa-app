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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $surat = $this->surat;
        $surat->loadMissing(['suratType']);

        $title = $surat->suratType?->name ?? 'Surat';

        $mail = (new MailMessage)
            ->subject("Pengajuan Disetujui Sekretaris: {$title} (#{$surat->id})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga') . '.')
            ->line('Pengajuan surat Anda sudah disetujui oleh Sekretaris Desa.')
            ->line('Status saat ini: Menunggu tanda tangan Kepala Desa.')
            ->line('Jenis Surat: ' . $title);

        if (!empty($surat->notes)) {
            $mail->line('Catatan: ' . $surat->notes);
        }

        return $mail
            ->action('Lihat Status Pengajuan', route('warga.surat.index'))
            ->line('Terima kasih.');
    }
}
