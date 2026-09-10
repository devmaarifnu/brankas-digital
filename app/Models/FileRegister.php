<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRegister extends Model
{
    use HasFactory;
    protected $table = 'file_register';
    protected $primaryKey = 'id_file';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_satpen',
        'daerah',
        'mapfile',
        'nm_lembaga',
        'nomor_surat',
        'tgl_surat',
        'filesurat',
    ];
}
