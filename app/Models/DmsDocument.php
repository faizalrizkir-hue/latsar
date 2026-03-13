<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmsDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year',
        'type',
        'doc_no',
        'title',
        'description',
        'tag',
        'status',
        'uploader',
        'updated_by',
    ];

    public function files()
    {
        return $this->hasMany(DmsFile::class, 'document_id');
    }
}
