<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'template_html',
        'template_doc_path',
        'template_doc_original_name',
        'required_documents',
        'input_fields',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'input_fields' => 'array',
    ];
}
