<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DataAsetLembaga extends Model
{
    protected $table = 'data_aset_lembaga';

    protected $fillable = [
        'nama_aset',
        'jenis_aset',
        'nilai_aset',
        'lokasi',
        'jenis_barang',
        'nama_barang',
        'merek',
        'nomor_seri_model',
        'nomor_registrasi',
        'sumber_perolehan',
        'tgl_perolehan',
        'kondisi_aset',
        'posisi_aset',
        'nama_ruangan',
        'nama_penerima',
        'no_telp_penerima',
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

    // Accessor for nama_petugas to maintain compatibility with views
    public function getNamaPetugasAttribute()
    {
        return $this->user->name ?? '-';
    }

    protected $casts = [
        'warna_merah' => 'boolean',
        'tgl_perolehan' => 'date',
        'tgl_input' => 'date',
    ];

    public function getUsiaBarangAttribute()
    {
        if (!$this->tgl_perolehan) return '-';
        $diff = Carbon::parse($this->tgl_perolehan)->diff(Carbon::now());
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' Thn';
        if ($diff->m > 0) $parts[] = $diff->m . ' Bln';
        if ($diff->d > 0 && empty($parts)) $parts[] = $diff->d . ' Hari';
        return empty($parts) ? '0 Bln' : implode(' ', $parts);
    }

    public function handovers()
    {
        return $this->hasMany(RecordOfHandover::class, 'ref_id')->where('kategori', 'Data Aset Lembaga');
    }
}