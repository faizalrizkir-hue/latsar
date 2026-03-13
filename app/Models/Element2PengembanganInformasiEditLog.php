<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PengembanganInformasiEditLog extends Model
{
    protected $table = 'element2_pengembangan_informasi_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
