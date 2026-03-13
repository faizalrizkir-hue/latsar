<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmsFile extends Model
{
    protected $fillable = [
        'document_id',
        'doc_no',
        'doc_name',
        'file_name',
        'file_path',
        'file_size',
        'storage_driver',
        'mime_type',
        'size_bytes',
        'uploaded_at',
        'tag',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(DmsDocument::class, 'document_id');
    }
}
