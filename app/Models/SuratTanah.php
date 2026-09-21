<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTanah extends Model
{
    protected $table = 'surat_tanah';

    protected $fillable = [
        'jenis_sertifikat',
        'nomor_sertifikat',
        'luas',
        'nama_sertifikat',
        'nama_dokumen',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'lokasi',
        'alamat',
        'atas_nama',
        'file_dokumen',
        'user_id',
        'tgl_input',
        'keterangan',
        'status_handover',
        'warna_merah',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    protected $appends = ['nama_petugas'];

    public function getNamaPetugasAttribute()
    {
        return $this->user->name ?? '-';
    }

    protected $casts = [
        'warna_merah' => 'boolean',
        'tgl_input' => 'date',
    ];

    public function handovers()
    {
        return $this->hasMany(RecordOfHandover::class, 'ref_id')->where('kategori', 'Arsip Surat Tanah')->orderBy('created_at', 'desc');
    }
}