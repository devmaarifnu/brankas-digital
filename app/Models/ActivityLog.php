<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'menu_name',
        'record_id',
        'changes',
        'ip_address',
        'user_agent',
        'url',
        'route',
        'description',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'user_id', 'id_user');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
