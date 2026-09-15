<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktaNotaris extends Model
{
    protected $table = 'akta_notaris';

    protected $fillable = [
        'jenis_dokumen',
        'jenis_sertifikat',
        'nomor_dokumen',
        'nomor_akta',
        'nomor_sertifikat',
        'nama_dokumen',
        'nama_sertifikat',
        'tgl_dokumen',
        'tanggal_akta',
        'nama_notaris',
        'alamat_notaris',
        'alamat',
        'telp_notaris',
        'user_id',
        'tgl_input',
        'luas',
        'perihal',
        'file_dokumen',
        'keterangan',
        'status_handover',
        'warna_merah',
    ];

    protected $casts = [
        'warna_merah' => 'boolean',
        'tgl_dokumen' => 'date',
        'tanggal_akta' => 'date',
        'tgl_input' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}