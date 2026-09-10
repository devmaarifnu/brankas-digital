<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualNPSN extends Model
{
    use HasFactory;
    protected $table = 'virtual_npsn';
    protected $primaryKey = 'id_npsn';
    protected $hidden = ['created_at', 'updated_at', 'id_prov', 'id_kab', 'id_jenjang'];
    protected $fillable = [
        'id_prov',
        'id_kab',
        'id_jenjang',
        'nomor_virtual',
        'nama_sekolah',
        'alamat',
        'email',
        'nik_kepsek',
        'expired_after',
        'accepted_date',
        'actived_date'
    ];

    public function provinsi() {
        return $this->belongsTo(Provinsi::class, 'id_prov');
    }

    public function kabupaten() {
        return $this->belongsTo(Kabupaten::class, 'id_kab');
    }

    public function jenjang() {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }
}
