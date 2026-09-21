<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasOne;


class Provinsi extends Model
{
    use HasFactory;
    protected $table = 'provinsi';
    protected $primaryKey = 'id_prov';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'kode_prov',
        'nm_prov',
    ];
    
    public function profile(): hasOne
    {
        return $this->hasOne(ProfilePengurusWilayah::class, 'id_pw', 'id_prov');
    }

}
