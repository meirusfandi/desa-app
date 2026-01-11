<?php

namespace App\Notifications;

use App\Models\SuratRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuratRejectedToWarga extends Notification
{
    use Queueable;

    public function __construct(private readonly SuratRequest $surat, private readonly ?string $reason = null)
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
        $reason = $this->reason ?? $surat->notes ?? '-';

        return (new MailMessage)
            ->subject("Surat Ditolak: {$title} (#{$surat->id})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Warga') . '.')
            ->line('Maaf, pengajuan surat Anda (' . $title . ') DITOLAK.')
            ->line('Alasan penolakan: ' . $reason)
            ->action('Lihat Detail', route('warga.surat.index'))
            ->line('Silakan ajukan ulang jika diperlukan.');
    }

    public function toFonnte(object $notifiable): string
    {
        $surat = $this->surat;
        $title = $surat->suratType?->name ?? 'Surat';
        $name = $notifiable->name ?? 'Warga';
        $reason = $this->reason ?? $surat->notes ?? '-';
        
        return "Halo {$name},\n\nMohon maaf, pengajuan surat Anda *{$title}* (#{$surat->id}) *DITOLAK*.\n\nAlasan: {$reason}\n\nSilakan cek dashboard untuk detailnya.\n\nTerima kasih.\n- Pemerintah Desa";
    }
}
