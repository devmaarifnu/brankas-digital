<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KeuanganRekapBulanan extends Model {
    protected $table = 'keuangan_rekap_bulanan';
    protected $fillable = ['bulan','tahun','total_pemasukan','total_pengeluaran','saldo','keterangan','file_rekap'];
    protected $casts = ['total_pemasukan'=>'decimal:2','total_pengeluaran'=>'decimal:2','saldo'=>'decimal:2'];
}
