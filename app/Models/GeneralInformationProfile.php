<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralInformationProfile extends Model
{
    protected $fillable = [
        'dasar_hukum_penilaian',
        'pemerintah_daerah',
        'nama_skpd',
        'bidang',
        'kepala_pemerintah_daerah',
        'undang_undang_pendirian',
        'visi',
        'misi',
        'inspektur',
        'alamat_kantor',
        'jumlah_kantor_wilayah',
        'kontak',
        'website',
        'updated_by',
    ];
}
