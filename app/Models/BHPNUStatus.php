<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BHPNUStatus extends Model
{
    use HasFactory;
    protected $table = 'bhpnu_status';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_bhpnu',
        'statusType',
        'icon',
        'textstatus',
        'status',
        'keterangan',
    ];
}
