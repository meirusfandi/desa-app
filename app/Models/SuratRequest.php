<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratRequest extends Model
{
    protected $fillable = [
        'user_id',
        'surat_type_id',
        'status',
        'notes',
        'signed_file'
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
}
