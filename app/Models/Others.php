<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Others extends Model
{
    use HasFactory;

    protected $table = 'data_lainnya';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_satpen',
        'npyp',
        'naungan',
        'no_sk_pendirian',
        'tgl_sk_pendirian',
        'no_sk_operasional',
        'tgl_sk_operasional',
        'link_sk_operational',
        'akreditasi',
        'website',
        'lingkungan_satpen',
        'last_sinkron',
        'status_sinkron',
    ];


    // Relasi ke Satpen
    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_satpen', 'id_satpen');
    }

}