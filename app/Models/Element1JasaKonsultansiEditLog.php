<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element1JasaKonsultansiEditLog extends Model
{
    protected $table = 'element1_jasa_konsultansi_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
