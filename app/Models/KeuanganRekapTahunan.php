<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KeuanganRekapTahunan extends Model {
    protected $table = 'keuangan_rekap_tahunan';
    protected $fillable = ['tahun','total_pemasukan','total_pengeluaran','saldo','keterangan','file_rekap'];
    protected $casts = ['total_pemasukan'=>'decimal:2','total_pengeluaran'=>'decimal:2','saldo'=>'decimal:2'];
}
