<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element4PengembanganSdmProfesionalApipEditLog extends Model
{
    protected $table = 'element4_pengembangan_sdm_profesional_apip_edit_logs';

    protected $fillable = [
        'row_id',
        'pernyataan',
        'username',
        'display_name',
        'action',
    ];
}
