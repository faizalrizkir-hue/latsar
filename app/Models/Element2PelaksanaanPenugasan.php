<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PelaksanaanPenugasan extends Model
{
    protected $table = 'element2_pelaksanaan_penugasan';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'pernyataan',
        'level',
        'skor',
        'analisis_bukti',
        'analisis_nilai',
        'grad_l1_catatan',
        'grad_l2_catatan',
        'grad_l3_catatan',
        'grad_l4_catatan',
        'grad_l5_catatan',
        'evidence',
        'verified',
        'dokumen_path',
        'doc_file_ids',
        'level_validation_state',
        'verify_note',
    ];

    protected $casts = [
        'doc_file_ids' => 'array',
        'level_validation_state' => 'array',
        'verified' => 'boolean',
    ];
}
