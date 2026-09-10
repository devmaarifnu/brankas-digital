<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PTK extends Model
{
    use HasFactory;
    
    protected $table = 'ptk';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_npyp',
        'id_satpen',
        'nik',
        'nama_ptk',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_ibu',
        'agama',
        'kebutuhan_khusus',
        'status_perkawinan',
        'email',
        'kabupaten_kota',
        'kecamatan',
        'desa_kelurahan',
        'alamat',
        'kode_pos',
        'jenis_ptk',
        'status_kepegawaian',
        'nip',
        'lembaga_pengangkat',
        'no_sk_pengangkatan',
        'tmt_pengangkatan',
        'sumber_gaji',
        'lisensi_kepala_sekolah',
        'nomor_surat_tugas',
        'tanggal_surat_tugas',
        'tmt_tugas',
        'upload_sk',
        'status_ajuan',
        'tanggal_verifikasi',
        'tanggal_revisi',
        'tanggal_proses',
        'tanggal_approve',
        'tanggal_dikeluarkan',
        'verifikator_id',
        'approver_id',
        'keterangan_revisi',
        'nomor_sk_keluar',
        'catatan_verifikator'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pengangkatan' => 'date',
        'tanggal_surat_tugas' => 'date',
        'tmt_tugas' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_revisi' => 'datetime',
        'tanggal_proses' => 'datetime',
        'tanggal_approve' => 'datetime',
        'tanggal_dikeluarkan' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Enum values for validation
    const JENIS_KELAMIN = [
        'Laki-Laki',
        'Perempuan'
    ];

    const AGAMA = [
        'Islam',
        'Kristen',
        'Katolik',
        'Hindu',
        'Buddha',
        'Konghucu'
    ];

    const KEBUTUHAN_KHUSUS = [
        'Tidak ada',
        'A - Tuna Netra',
        'B - Tuna Rungu',
        'C - Tuna Grahita Ringan',
        'C1 - Tuna Grahita Sedang',
        'D - Tuna Daksa Ringan',
        'E - Tuna Laras',
        'F - Tuna Wicara',
        'H - Hiperaktif',
        'I - Cerdas Istimewa',
        'J - Bakat Istimewa',
        'K - Kesulitan Belajar',
        'N - Narkoba',
        'O - Indigo',
        'P - Down Sindrome',
        'Q - Autis',
        'Lainnya'
    ];

    const STATUS_PERKAWINAN = [
        'Menikah',
        'Belum Menikah',
        'Duda atau Lajang'
    ];

    const JENIS_PTK = [
        'Guru Kelas',
        'Guru Mapel',
        'Guru BK',
        'Guru Pendamping Khusus',
        'Tenaga Administrasi Sekolah',
        'Guru TIK',
        'Laboran',
        'Tenaga Perpustakaan',
        'Academic Advisor',
        'Academic Spesialis',
        'Curiculum Development Advisor',
        'Kindegarten Teacher',
        'Management Advisor',
        'Playgroup Teacher',
        'Principal',
        'Teaching Assistant',
        'Vice Principal',
        'Tukang Kebun',
        'Penjaga Sekolah',
        'Petugas Keamanan',
        'Pesuruh/Office Boy',
        'Kepala Sekolah',
        'Terapis',
        'Guru Pengganti',
        'Pengawas Paud Dikmas',
        'Penilik',
        'Guru Pembimbing Khusus',
        'Instruktur Kejuruan',
        'Instruktur',
        'Penguji',
        'Master Penguji',
        'Tutor',
        'Pamong Belajar',
        'Tenaga Kependidikan',
        'Pengawas'
    ];

    const STATUS_KEPEGAWAIAN = [
        'PNS',
        'PNS Diperbantukan',
        'PNS Depag',
        'GTY/PTY',
        'Honor Daerah Tk. 1 Provinsi',
        'Honor Daerah Tk. 2 Kab/Kota',
        'Guru Honor Sekolah',
        'Tenaga Honor Sekolah',
        'CPNS',
        'PPPK',
        'PPNPN',
        'Guru Pengganti',
        'Kontrak Kerja WNA'
    ];

    const LEMBAGA_PENGANGKAT = [
        'Pemerintah Pusat',
        'Pemerintah Provinsi',
        'Pemerintah Kab/Kota',
        'Ketua Yayasan',
        'Kepala Sekolah',
        'Lainnya'
    ];

    const SUMBER_GAJI = [
        'APBN',
        'APBD Provinsi',
        'APBD Kab/Kota',
        'Yayasan',
        'Sekolah',
        'Lembaga Donor',
        'Lainnya'
    ];

    const LISENSI_KEPALA_SEKOLAH = [
        'Sudah',
        'Belum'
    ];

    const STATUS_AJUAN = [
        'verifikasi',
        'revisi',
        'proses',
        'approve',
        'dikeluarkan'
    ];

    // Relationships
    public function npyp()
    {
        return $this->belongsTo(NPYP::class, 'id_npyp', 'id_npyp');
    }

    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_satpen', 'id_satpen');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_ajuan', $status);
    }

    public function scopeByNPYP($query, $npyp_id)
    {
        return $query->where('id_npyp', $npyp_id);
    }

    public function scopeBySatpen($query, $satpen_id)
    {
        return $query->where('id_satpen', $satpen_id);
    }

    public function scopeByJenisPTK($query, $jenis)
    {
        return $query->where('jenis_ptk', $jenis);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'verifikasi' => 'bg-warning',
            'revisi' => 'bg-danger',
            'proses' => 'bg-info',
            'approve' => 'bg-success',
            'dikeluarkan' => 'bg-primary'
        ];

        return $badges[$this->status_ajuan] ?? 'bg-secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'verifikasi' => 'Menunggu Verifikasi',
            'revisi' => 'Perlu Revisi',
            'proses' => 'Dalam Proses',
            'approve' => 'Disetujui',
            'dikeluarkan' => 'SK Dikeluarkan'
        ];

        return $labels[$this->status_ajuan] ?? 'Status Tidak Diketahui';
    }

    // Mutators
    public function setNikAttribute($value)
    {
        $this->attributes['nik'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function setNamaPtkAttribute($value)
    {
        $this->attributes['nama_ptk'] = ucwords(strtolower($value));
    }
}