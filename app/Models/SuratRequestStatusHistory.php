<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratRequestStatusHistory extends Model
{
    protected $fillable = [
        'surat_request_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by_user_id',
    ];

    public function suratRequest()
    {
        return $this->belongsTo(SuratRequest::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
