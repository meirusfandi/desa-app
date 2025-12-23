<?php

namespace App\Services;

use App\Models\SuratRequest;
use App\Models\User;
use App\Notifications\NewSuratRequestSubmittedToSekretaris;
use App\Notifications\SuratApprovedToKepalaDesa;
use App\Notifications\SuratApprovedToWarga;
use App\Notifications\SuratRejectedToWarga;
use App\Notifications\SuratSignedToWarga;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotificationService
{

    /**
     * Warga creates request -> email to sekretaris.
     */
    public function emailSekretarisOnWargaSubmitted(SuratRequest $surat): void
    {
        $surat->loadMissing(['user', 'suratType']);

        $recipients = $this->usersByRole('sekretaris');
        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewSuratRequestSubmittedToSekretaris($surat));
    }

    /**
     * Sekretaris approve -> email to warga + kepala desa (to sign).
     */
    public function emailOnSekretarisApproved(SuratRequest $surat): void
    {
        $surat->loadMissing(['user', 'suratType']);

        $warga = $surat->user;
        if ($warga && $warga->email) {
            $warga->notify(new SuratApprovedToWarga($surat));
        }

        $kepalaDesa = $this->usersByRole('kepala_desa');
        if ($kepalaDesa->isNotEmpty()) {
            Notification::send($kepalaDesa, new SuratApprovedToKepalaDesa($surat));
        }
    }

    /**
     * Sekretaris reject -> email to warga with resubmit/update guidance.
     */
    public function emailOnSekretarisRejected(SuratRequest $surat): void
    {
        $surat->loadMissing(['user', 'suratType']);

        $warga = $surat->user;
        if (! $warga || ! $warga->email) {
            return;
        }

        $warga->notify(new SuratRejectedToWarga($surat));
    }

    /**
     * Kepala desa signs -> email to warga that signed PDF is ready.
     */
    public function emailOnKepalaDesaSigned(SuratRequest $surat): void
    {
        $surat->loadMissing(['user', 'suratType']);

        $warga = $surat->user;
        if (! $warga || ! $warga->email) {
            return;
        }

        $warga->notify(new SuratSignedToWarga($surat));
    }

    private function usersByRole(string $role): Collection
    {
        return User::role($role)
            ->whereNotNull('email')
            ->get();
    }
}
