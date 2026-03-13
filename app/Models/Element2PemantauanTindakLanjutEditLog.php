<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2PemantauanTindakLanjutEditLog extends Model
{
    protected $table = 'element2_pemantauan_tindak_lanjut_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
