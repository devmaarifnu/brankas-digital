<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSS extends Model
{
    use HasFactory;
    protected $table = 'oss_new';
    protected $primaryKey = 'id_oss';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'id_oss',
        'id_user',
        'email',
        'npwp',
        'no_whatsapp',
        'intansi_izin_lama',
        'nomor_izin_lama',
        'tgl_terbit_izin_lama',
        'tgl_expired_izin_lama',
        'file_izin_lama',
        'lokasi_usaha',
        'luas_lahan_usaha',
        'apakah_sudah_menempati_lahan',
        'status_lahan',
        'ms_instansi_izin',
        'ms_nomor_izin',
        'ms_tgl_terbit',
        'ms_tgl_expired',
        'ms_file_lampiran',
        'sw_pemilik_lahan',
        'sw_nomor_perjanjian',
        'sw_tgl_perjanjian',
        'sw_tgl_expired',
        'sw_file_lampiran',
        'pp_pemilik_lahan',
        'pp_nomor_perjanjian',
        'pp_tgl_perjanjian',
        'pp_tgl_expired',
        'pp_file_lampiran',
        'apakah_memerlukan_bangunan_baru',
        'sudah_ada_bangunan',
        'status_bangunan_usaha',
        'jumlah_bangunan',
        'apakah_memiliki_imb',
        'imb_jml_bangunan',
        'imb_pejabat_penerbit_izin',
        'imb_nomor',
        'imb_tgl_terbit',
        'imb_tgl_expired',
        'imb_file_lampiran',
        'apakah_memiliki_sertifikat_slf',
        'slf_pejabat_penerbit',
        'slf_nomor',
        'slf_tgl_terbit',
        'slf_tgl_expired',
        'slf_file_lampiran',
        'apakah_lokasi_sekolah_lintas_perbatasan',
        'alamat_sekolah',
        'propinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'kode_pos',
        'file_peta_polygon',
        'apakah_proyek_strategi_nasional',
        'rencana_teknis_bangunan',
        'kawasan_lokasi_usaha',
        'klu_nama_kawasan_industri',
        'apakah_memiliki_kkpr',
        'pejabat_penerbit_kkpr',
        'nomor_kkpr',
        'tgl_terbit_kkpr',
        'tgl_expired_kkpr',
        'file_lampiran_kkpr',
        'dri_pembelian_tanah',
        'dri_bangunan',
        'dri_mesin_dalam_negeri',
        'dri_mesin_impor',
        'dri_investasi',
        'dri_modal_kerja_3_bulan',
        'tgl_mulai_beroperasi',
        'jml_pegawai_pria',
        'jml_pegawai_wanita',
        'jml_pegawai_asing',
        'apakah_memiliki_izin_amdal',
        'amdal_pejabat_penerbit',
        'amdal_nomor_izin',
        'amdal_tgl_terbit',
        'amdal_tgl_expired',
        'amdal_file_lampiran',
        'apakah_memiliki_uklupl',
        'uklupl_pejabat_penerbit',
        'uklupl_nomor_izin',
        'uklupl_tgl_terbit',
        'uklupl_tgl_expired',
        'uklupl_file_lampiran',

        'bukti_bayar',
        'tanggal',
        'tgl_izin',
        'tgl_expired',
        'status',
    ];
    public function satpen()
    {
        return $this->belongsTo(Satpen::class, 'id_user', 'id_user');
    }
    public function ossstatus()
    {
        return $this->hasMany(OSSStatus::class, 'id_oss');
    }
    public function osstimeline()
    {
        return $this->hasMany(OSSTimeline::class, 'id_oss', 'id_oss');
    }

}
