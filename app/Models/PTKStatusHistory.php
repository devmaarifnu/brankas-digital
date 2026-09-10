<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PTKStatusHistory extends Model
{
    use HasFactory;
    
    protected $table = 'ptk_status_history';
    protected $primaryKey = 'id';
    
    // Disable updated_at since table only has created_at
    public $timestamps = false;
    
    protected $fillable = [
        'ptk_id',
        'status_from',
        'status_to',
        'keterangan',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Enum values for validation (same as PTK model)
    const STATUS_OPTIONS = [
        'verifikasi',
        'revisi',
        'proses',
        'approve',
        'dikeluarkan'
    ];

    // Relationships
    public function ptk()
    {
        return $this->belongsTo(PTK::class, 'ptk_id', 'id');
    }

    // Scopes
    public function scopeByPTK($query, $ptk_id)
    {
        return $query->where('ptk_id', $ptk_id);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_to', $status);
    }

    public function scopeByStatusFrom($query, $status)
    {
        return $query->where('status_from', $status);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    // Accessors
    public function getStatusFromLabelAttribute()
    {
        if (!$this->status_from) {
            return 'Status Awal';
        }

        $labels = [
            'verifikasi' => 'Menunggu Verifikasi',
            'revisi' => 'Perlu Revisi',
            'proses' => 'Dalam Proses',
            'approve' => 'Disetujui',
            'dikeluarkan' => 'SK Dikeluarkan'
        ];

        return $labels[$this->status_from] ?? 'Status Tidak Diketahui';
    }

    public function getStatusToLabelAttribute()
    {
        $labels = [
            'verifikasi' => 'Menunggu Verifikasi',
            'revisi' => 'Perlu Revisi',
            'proses' => 'Dalam Proses',
            'approve' => 'Disetujui',
            'dikeluarkan' => 'SK Dikeluarkan'
        ];

        return $labels[$this->status_to] ?? 'Status Tidak Diketahui';
    }

    public function getStatusFromBadgeAttribute()
    {
        if (!$this->status_from) {
            return 'bg-secondary';
        }

        $badges = [
            'verifikasi' => 'bg-warning',
            'revisi' => 'bg-danger',
            'proses' => 'bg-info',
            'approve' => 'bg-success',
            'dikeluarkan' => 'bg-primary'
        ];

        return $badges[$this->status_from] ?? 'bg-secondary';
    }

    public function getStatusToBadgeAttribute()
    {
        $badges = [
            'verifikasi' => 'bg-warning',
            'revisi' => 'bg-danger',
            'proses' => 'bg-info',
            'approve' => 'bg-success',
            'dikeluarkan' => 'bg-primary'
        ];

        return $badges[$this->status_to] ?? 'bg-secondary';
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d F Y H:i') : '-';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }

    // Static methods for creating history records
    public static function createHistory($ptk_id, $status_from, $status_to, $keterangan = null)
    {
        return self::create([
            'ptk_id' => $ptk_id,
            'status_from' => $status_from,
            'status_to' => $status_to,
            'keterangan' => $keterangan,
            'created_at' => now()
        ]);
    }

    public static function getStatusFlow($ptk_id)
    {
        return self::where('ptk_id', $ptk_id)
                   ->orderBy('created_at', 'asc')
                   ->get();
    }

    public static function getLastStatusChange($ptk_id)
    {
        return self::where('ptk_id', $ptk_id)
                   ->orderBy('created_at', 'desc')
                   ->first();
    }

    // Helper methods
    public function isStatusUpgrade()
    {
        $hierarchy = [
            'verifikasi' => 1,
            'proses' => 2,
            'approve' => 3,
            'dikeluarkan' => 4,
            'revisi' => 0 // Revisi is considered a downgrade
        ];

        if (!$this->status_from) {
            return true; // Initial status is always considered an upgrade
        }

        $from_level = $hierarchy[$this->status_from] ?? 0;
        $to_level = $hierarchy[$this->status_to] ?? 0;

        return $to_level > $from_level;
    }

    public function isRevision()
    {
        return $this->status_to === 'revisi';
    }

    public function isApproval()
    {
        return $this->status_to === 'approve';
    }

    public function isFinal()
    {
        return $this->status_to === 'dikeluarkan';
    }
}