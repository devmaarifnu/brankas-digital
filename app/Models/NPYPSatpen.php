<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NPYPSatpen extends Model
{
    use HasFactory;
    
    protected $table = 'npyp_satpen';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_npyp',
        'id_satpen',
        'assign_date',
    ];

    protected $casts = [
        'assign_date' => 'date',
    ];

    public function npyp()
    {
        return $this->belongsTo(NPYP::class, 'id_npyp');
    }

    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_satpen');
    }
}