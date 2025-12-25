<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WargaProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'kk',
        'alamat',
        'rt',
        'rw',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
