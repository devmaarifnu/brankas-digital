<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    use HasFactory;
    protected $table = 'informasi';
    protected $primaryKey = 'id_info';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'type',
        'slug',
        'headline',
        'tgl_upload',
        'content',
        'image',
        'tag',
    ];

    public function file()
    {
        return $this->hasMany(InformasiFile::class, 'id_info');
    }
}
