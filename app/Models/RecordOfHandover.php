<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordOfHandover extends Model
{
    protected $table = 'record_of_handover';

    protected $fillable = [
        'kategori',
        'nama_dokumen',
        'ref_id',
        'status',
        'nama_peminjam',
        'no_telp_peminjam',
        'nama_bank',
        'jangka_agunan',
        'penanggung_agunan',
        'no_telp_penanggung',
        'nama_penerima',
        'no_telp_penerima',
        'tgl_serahterima',
        'file_bukti',
        'catatan',
    ];

    protected $casts = [
        'tgl_serahterima' => 'date',
    ];
}