<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSSTimeline extends Model
{
    use HasFactory;
    protected $table = 'oss_timeline';
    protected $primaryKey = 'id_timeline';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_oss',
        'status_verifikasi',
        'tgl_verifikasi',
        'catatan',
        'link_pnbp',
        'link_catatan_pupr',
        'link_gistaru',
        'link_izin_terbit',
        'nomor_ku',
    ];
}
