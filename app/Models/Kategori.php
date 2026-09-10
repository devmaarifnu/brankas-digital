<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori_satpen';
    protected $primaryKey = 'id_kategori';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'nm_kategori',
        'konotasi',
        'keterangan',
    ];
}
