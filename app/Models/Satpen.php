<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satpen extends Model
{
    use HasFactory;
    protected $table = 'satpen';
    protected $primaryKey = 'id_satpen';
    protected $hidden = ['created_at', 'updated_at', 'id_prov', 'id_kab', 'id_kategori', 'id_jenjang', 'id_pc'];
    protected $fillable = [
        'id_user',
        'id_prov',
        'id_kab',
        'id_pc',
        'id_kategori',
        'id_jenjang',
        'npsn',
        'no_registrasi',
        'no_urut',
        'nm_satpen',
        'yayasan',
        'kepsek',
        'telpon',
        'email',
        'thn_berdiri',
        'alamat',
        'kelurahan',
        'kecamatan',
        'aset_tanah',
        'nm_pemilik',
        'tgl_registrasi',
        'status',
        'fax',
    ];

    public function provinsi() {
        return $this->belongsTo(Provinsi::class, 'id_prov');
    }

    public function kabupaten() {
        return $this->belongsTo(Kabupaten::class, 'id_kab');
    }

    public function cabang() {
        return $this->belongsTo(PengurusCabang::class, 'id_pc');
    }

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function jenjang() {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }

    public function timeline() {
        return $this->hasMany(Timeline::class, 'id_satpen');
    }

    public function file() {
        return $this->hasMany(FileUpload::class, 'id_satpen');
    }

    public function filereg() {
        return $this->hasMany(FileRegister::class, 'id_satpen');
    }
}
