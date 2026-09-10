<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KeuanganPengajuan extends Model {
    protected $table = 'keuangan_pengajuan';
    protected $fillable = ['judul','jumlah','keterangan','file_bukti','status'];
    protected $casts = ['jumlah' => 'decimal:2'];
}
