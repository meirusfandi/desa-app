<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SuratRequest extends Model
{
    protected $fillable = [
        'user_id',
        'surat_type_id',
        'status',
        'notes',
        'signed_file',
        'data',
        'signed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suratType()
    {
        return $this->belongsTo(SuratType::class);
    }

    public function files()
    {
        return $this->hasMany(SuratFile::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(SuratRequestStatusHistory::class)->latest();
    }

    protected static function booted(): void
    {
        static::created(function (SuratRequest $surat) {
            SuratRequestStatusHistory::create([
                'surat_request_id' => $surat->id,
                'from_status' => null,
                'to_status' => $surat->status,
                'notes' => $surat->notes,
                'changed_by_user_id' => Auth::id(),
            ]);
        });

        static::updated(function (SuratRequest $surat) {
            if (! $surat->wasChanged('status')) {
                return;
            }

            SuratRequestStatusHistory::create([
                'surat_request_id' => $surat->id,
                'from_status' => $surat->getOriginal('status'),
                'to_status' => $surat->status,
                'notes' => $surat->notes,
                'changed_by_user_id' => Auth::id(),
            ]);
        });
    }
}
