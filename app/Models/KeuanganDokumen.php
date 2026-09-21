<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KeuanganDokumen extends Model {
    protected $table = 'keuangan_dokumen';
    protected $fillable = ['jenis','periode','file_path','judul','keterangan'];
}
