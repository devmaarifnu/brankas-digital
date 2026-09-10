<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coretax extends Model
{
    use HasFactory;
    protected $table = 'coretax';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_user',
        'id_pw',
        'id_pc',
        'nitku',
        'nama_pic',
        'nik_pic',
        'whatsapp_pic',
        'tanggal',
        'tgl_submit',
        'tgl_acc',
        'tgl_expiry',
        'new_request',
        'npwp_lama',
        'status',
    ];
    public function satpen(): BelongsTo
    {
        return $this->belongsTo(Satpen::class, 'id_user', 'id_user');
    }
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'id_pw', 'id_prov');
    }
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(PengurusCabang::class, 'id_pc', 'id_pc');
    }
    public function corestatus(): HasMany
    {
        return $this->hasMany(CoretaxStatus::class, 'id_coretax');
    }
}
