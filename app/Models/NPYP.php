<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NPYP extends Model
{
    use HasFactory;
    
    protected $table = 'npyp';
    protected $primaryKey = 'id_npyp';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_pw',
        'id_pc',
        'nomor_npyp',
        'nama_npyp',
        'nama_operator',
        'nomor_operator',
    ];

    public function pengurusWilayah()
    {
        return $this->belongsTo(Provinsi::class, 'id_pw');
    }

    public function pengurusCabang()
    {
        return $this->belongsTo(PengurusCabang::class, 'id_pc');
    }

    public function npypSatpen()
    {
        return $this->hasMany(NPYPSatpen::class, 'id_npyp');
    }
}