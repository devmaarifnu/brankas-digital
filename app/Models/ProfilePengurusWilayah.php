<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePengurusWilayah extends Model
{
    protected $table = 'profile_pengurus_wilayah';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_pw',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'lintang',
        'bujur',
        'website',
        'ketua',
        'wakil_ketua',
        'bendahara',
        'sekretaris',
        'telp_ketua',
        'telp_wakil',
        'telp_bendahara',
        'telp_sekretaris',
        'masa_khidmat',
    ];
}
