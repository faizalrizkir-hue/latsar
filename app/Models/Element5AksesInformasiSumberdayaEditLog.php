<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element5AksesInformasiSumberdayaEditLog extends Model
{
    protected $table = 'element5_akses_informasi_sumberdaya_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
