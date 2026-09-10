<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BHPNU extends Model
{
    use HasFactory;
    protected $table = 'bhpnu';
    protected $primaryKey = 'id_bhpnu';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_user',
        'bukti_bayar',
        'no_resi',
        'tanggal',
        'tgl_dikirim',
        'tgl_expired',
        'status',
    ];
    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_user', 'id_user');
    }
    public function bhpnustatus()
    {
        return $this->hasMany(BHPNUStatus::class, 'id_bhpnu');
    }
}
