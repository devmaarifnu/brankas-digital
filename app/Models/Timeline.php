<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;
    protected $table = 'timeline_reg';
    protected $primaryKey = 'id_timeline';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_satpen',
        'status_verifikasi',
        'tgl_status',
        'keterangan',
    ];
}
