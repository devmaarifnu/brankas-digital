<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_pelajaran';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'tapel_dapo',
        'nama_tapel',
    ];

    public function pdptk()
    {
        return $this->hasMany(Pdptk::class, 'tapel', 'tapel_dapo');
    }
}
?>