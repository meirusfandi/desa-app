<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuratRejectedToWarga extends Notification
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
            ->subject("Pengajuan Ditolak: {$title} (#{$surat->id})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga') . '.')
            ->line('Maaf, pengajuan surat Anda ditolak oleh Sekretaris Desa.')
            ->line('Alasan/Catatan: ' . ($surat->notes ?: '-'))
            ->line('Silakan perbaiki data/dokumen yang diperlukan, lalu ajukan ulang atau perbarui pengajuan sesuai arahan petugas.')
            ->action('Buat Pengajuan Baru', route('warga.surat.create'))
            ->line('Jika Anda yakin ini perlu diperbarui (bukan ajukan ulang), silakan hubungi petugas desa untuk arahan.');

        return $mail;
    }
}
