<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoretaxStatus extends Model
{
    use HasFactory;
    protected $table = 'coretax_status';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_coretax',
        'statusType',
        'icon',
        'textstatus',
        'status',
        'keterangan',
    ];
}
