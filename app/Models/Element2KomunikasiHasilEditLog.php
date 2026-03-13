<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element2KomunikasiHasilEditLog extends Model
{
    protected $table = 'element2_komunikasi_hasil_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
