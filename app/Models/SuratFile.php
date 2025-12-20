<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratFile extends Model
{
    protected $fillable = [
        'surat_request_id',
        'file_path',
        'file_type'
    ];

    public function suratRequest()
    {
        return $this->belongsTo(SuratRequest::class);
    }
}
