<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSSStatus extends Model
{
    use HasFactory;
    protected $table = 'oss_status';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_oss',
        'statusType',
        'icon',
        'textstatus',
        'status',
        'keterangan',
    ];
}
