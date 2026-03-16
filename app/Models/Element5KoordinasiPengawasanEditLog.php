<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element5KoordinasiPengawasanEditLog extends Model
{
    protected $table = 'element5_koordinasi_pengawasan_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
