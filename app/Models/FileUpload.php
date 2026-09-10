<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;
    protected $table = 'file_upload';
    protected $primaryKey = 'id_file';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_satpen',
        'typefile',
        'qrcode',
        'nm_file',
        'tgl_file',
    ];
}
