<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PDPTK extends Model
{
    use HasFactory;

    protected $table = 'pdptk';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_satpen',
        'tapel',
        'pd_lk',
        'pd_pr',
        'jml_pd',
        'guru_lk',
        'guru_pr',
        'jml_guru',
        'tendik_lk',
        'tendik_pr',
        'jml_tendik',
        'last_sinkron',
        'status_sinkron',
    ];

    // Relasi ke Satpen
    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_satpen', 'id_satpen');
    }

    // Relasi ke TahunPelajaran
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tapel', 'tapel_dapo');
    }
}