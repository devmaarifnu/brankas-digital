<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKendaraan extends Model
{
    protected $table = 'surat_kendaraan';

    protected $fillable = [
        'jenis_surat',
        'data_aset_id',
        'nama_kendaraan',
        'nama_pemilik',
        'no_plat',
        'no_rangka',
        'no_mesin',
        'file_dokumen',
        'tgl_input',
        'user_id',
        'nama_petugas',
        'keterangan',
        'status_handover',
        'warna_merah',
    ];

    protected $casts = [
        'warna_merah' => 'boolean',
        'tgl_input' => 'date',
    ];

    public function dataAset()
    {
        return $this->belongsTo(DataAsetLembaga::class, 'data_aset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function getPetugasNameAttribute()
    {
        return $this->nama_petugas ?: ($this->user->name ?? '-');
    }

    public function handovers()
    {
        return $this->hasMany(RecordOfHandover::class, 'ref_id')->where('kategori', 'Arsip Surat Kendaraan')->orderBy('created_at', 'desc');
    }
}
