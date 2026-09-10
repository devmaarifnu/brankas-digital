<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;
    protected $table = 'kabupaten';
    protected $primaryKey = 'id_kab';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_prov',
        'nama_kab',
    ];
    public function prov()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov');
    }
}
