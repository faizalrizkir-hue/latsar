<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element4DukunganTikEditLog extends Model
{
    protected $table = 'element4_dukungan_tik_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
