<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
    protected $table = 'access_token';
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'token',
        'hashtype',
        'expires_at',
    ];

    public static function findByToken(string $token) {
        return self::where('token', '=', $token)->first();
    }
}
