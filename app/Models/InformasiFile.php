<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiFile extends Model
{
    use HasFactory;
    protected $table = 'informasi_file';
    protected $primaryKey = 'id_file';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_info',
        'fileupload',
    ];
}
