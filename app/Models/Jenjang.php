<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenjang extends Model
{
    use HasFactory;
    protected $table = 'jenjang_pendidikan';
    protected $primaryKey = 'id_jenjang';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'nm_jenjang',
        'lembaga',
        'keterangan',
        'lembaga',
    ];
}
