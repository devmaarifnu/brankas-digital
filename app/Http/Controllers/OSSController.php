<?php

namespace App\Http\Controllers;

use App\Http\Requests\OSSRequest;
use App\Models\OSS;
use App\Models\OSSStatus;
use App\Models\OSSTimeline;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OSSController extends Controller
{
    public function landOSSRequest() {

        $oss = OSS::with(["satpen:id_satpen,id_user,no_registrasi,nm_satpen", "ossstatus", "osstimeline" => function ($query) {
            $query->orderBy('id_timeline', 'DESC')
                ->limit(1);
            }])->where('id_user', '=', auth()->user()->id_user)
            ->orderBy('id_oss', 'DESC')
            ->first();

        return view('oss.permohonan', compact('oss'));
    }

    public function detailOSSQuesioner(string $ossId) {

        $oss = OSS::with(["satpen:id_satpen,id_user,no_registrasi,nm_satpen","osstimeline"])
            ->where([
                'id_user' => auth()->user()->id_user,
                'id_oss' => $ossId,
            ])
            ->orderBy('id_oss', 'DESC')
            ->first();

        return view('oss.detail', compact('oss'));
    }

    public function newOSSRequest() {

        $oss = OSS::create([
            'id_user' => auth()->user()->id_user,
            'email' => '',
            'npwp' => '',
            'no_whatsapp' => '',
            'intansi_izin_lama' => '',
            'nomor_izin_lama' => '',
            'tgl_terbit_izin_lama' => null,
            'tgl_expired_izin_lama' => null,
            'file_izin_lama' => '',
            'lokasi_usaha' => null,
            'luas_lahan_usaha' => null,
            'apakah_sudah_menempati_lahan' => null,
            'status_lahan' => null,
            'ms_instansi_izin' => '',
            'ms_nomor_izin' => '',
            'ms_tgl_terbit' => null,
            'ms_tgl_expired' => null,
            'ms_file_lampiran' => '',
            'sw_pemilik_lahan' => '',
            'sw_nomor_perjanjian' => '',
            'sw_tgl_perjanjian' => null,
            'sw_tgl_expired' => null,
            'sw_file_lampiran' => '',
            'pp_pemilik_lahan' => '',
            'pp_nomor_perjanjian' => '',
            'pp_tgl_perjanjian' => null,
            'pp_tgl_expired' => null,
            'pp_file_lampiran' => '',
            'apakah_memerlukan_bangunan_baru' => null,
            'sudah_ada_bangunan' => null,
            'status_bangunan_usaha' => null,
            'jumlah_bangunan' => null,
            'apakah_memiliki_imb' => null,
            'imb_jml_bangunan' => null,
            'imb_pejabat_penerbit_izin' => '',
            'imb_nomor' => '',
            'imb_tgl_terbit' => null,
            'imb_tgl_expired' => null,
            'imb_file_lampiran' => '',
            'apakah_memiliki_sertifikat_slf' => null,
            'slf_pejabat_penerbit' => '',
            'slf_nomor' => '',
            'slf_tgl_terbit' => null,
            'slf_tgl_expired' => null,
            'slf_file_lampiran' => '',
            'apakah_lokasi_sekolah_lintas_perbatasan' => null,
            'alamat_sekolah' => '',
            'propinsi' => '',
            'kabupaten' => '',
            'kecamatan' => '',
            'kelurahan' => '',
            'kode_pos' => '',
            'file_peta_polygon' => '',
            'apakah_proyek_strategi_nasional' => null,
            'rencana_teknis_bangunan' => '',
            'kawasan_lokasi_usaha' => null,
            'klu_nama_kawasan_industri' => '',
            'apakah_memiliki_kkpr' => null,
            'pejabat_penerbit_kkpr' => '',
            'nomor_kkpr' => '',
            'tgl_terbit_kkpr' => null,
            'tgl_expired_kkpr' => null,
            'file_lampiran_kkpr' => '',
            'dri_pembelian_tanah' => '',
            'dri_bangunan' => '',
            'dri_mesin_dalam_negeri' => '',
            'dri_mesin_impor' => '',
            'dri_investasi' => '',
            'dri_modal_kerja_3_bulan' => '',
            'tgl_mulai_beroperasi' => null,
            'jml_pegawai_pria' => null,
            'jml_pegawai_wanita' => null,
            'jml_pegawai_asing' => null,
            'apakah_memiliki_izin_amdal' => null,
            'amdal_pejabat_penerbit' => '',
            'amdal_nomor_izin' => '',
            'amdal_tgl_terbit' => null,
            'amdal_tgl_expired' => null,
            'amdal_file_lampiran' => '',
            'apakah_memiliki_uklupl' => null,
            'uklupl_pejabat_penerbit' => '',
            'uklupl_nomor_izin' => '',
            'uklupl_tgl_terbit' => null,
            'uklupl_tgl_expired' => null,
            'uklupl_file_lampiran' => '',


            'bukti_bayar' => '',
            'tanggal' => Carbon::now(),
            'status' => 'mengisi persyaratan',
        ]);

        OSSStatus::insert([
           [
               'id_oss' => $oss->id_oss,
               'statusType' => 'mengisi persyaratan',
               'icon' => 'ti-keyboard',
               'textstatus' => 'Mengisi Persyaratan',
               'status' => 'success',
           ],[
               'id_oss' => $oss->id_oss,
               'statusType' => 'verifikasi',
               'icon' => 'ti-building-bank',
               'textstatus' => 'Verifikasi',
               'status' => null,
           ],[
               'id_oss' => $oss->id_oss,
               'statusType' => 'perbaikan',
               'icon' => 'ti-checkup-list',
               'textstatus' => 'Diterima Verifikator',
               'status' => null,
           ],[
               'id_oss' => $oss->id_oss,
               'statusType' => 'dokumen diproses',
               'icon' => 'ti-manual-gearbox',
               'textstatus' => 'Sedang Diproses',
               'status' => null,
           ],[
               'id_oss' => $oss->id_oss,
               'statusType' => 'izin terbit',
               'icon' => 'ti-fingerprint',
               'textstatus' => 'Izin Telah Terbit',
               'status' => null,
           ],
        ]);

        OSSTimeline::create([
            'id_oss' => $oss->id_oss,
            'status_verifikasi' => 'mengisi persyaratan',
            'tgl_verifikasi' => Carbon::now(),
            'catatan' => 'permohonan pengajuan oss baru',
            'link_pnbp' => null,
            'link_catatan_pupr' => null,
            'link_gistaru' => null,
            'link_izin_terbit' => null,
            'nomor_ku' => null,
        ]);

        return redirect()->back();
    }

    public function storedOSSRequest(OSSRequest $request, OSS $oss) {
        $path_bukti_bayar = "file-bukti-bayar";
        $path_file_izin_lama = "file-izin-lama";
        $path_file_peta_polygon = "file-peta-polygon";
        $path_ms_file_lampiran = "file-ms-lampiran";
        $path_sw_file_lampiran = "file-sw-lampiran";
        $path_pp_file_lampiran = "file-pp-lampiran";
        $path_imb_file_lampiran = "file-imb-lampiran";
        $path_slf_file_lampiran = "file-slf-lampiran";
        $path_rencana_teknis_bangunan = "file-rencana-teknis-bangunan";
        $path_file_lampiran_kkpr = "file-lampiran-kkpr";
        $path_amdal_file_lampiran = "file-amdal-lampiran";
        $path_uklupl_lampiran = "file-uklupl-lampiran";

        $no_registrasi = substr(Str::uuid()->toString(), 0, 4).'-'
                            .$oss->satpen->no_registrasi;
        $filename_bukti_bayar = $oss->bukti_bayar;
        $filename_file_izin_lama = $oss->file_izin_lama;
        $filename_file_peta_polygon = $oss->file_peta_polygon;
        $filename_rencana_teknis_bangunan = $oss->rencana_teknis_bangunan;
        try {
            if ($request->file('bukti_bayar') && $request->file('bukti_bayar')->isValid()) {
                $originalName = $no_registrasi.'~'.$request->file('bukti_bayar')->getClientOriginalName();
                $filename_bukti_bayar = Storage::disk('oss-doc')->putFileAs(
                    $path_bukti_bayar,
                    $request->file('bukti_bayar'),
                    $originalName
                );
                Storage::disk("oss-doc")->delete($path_bukti_bayar, $oss->bukti_bayar);
            }

            if ($request->file('file_izin_lama') && $request->file('file_izin_lama')->isValid()) {
                $originalName = $no_registrasi.'~'.$request->file('file_izin_lama')->getClientOriginalName();
                $filename_file_izin_lama = Storage::disk('oss-doc')->putFileAs(
                    $path_file_izin_lama,
                    $request->file('file_izin_lama'),
                    $originalName
                );
                Storage::disk("oss-doc")->delete($path_file_izin_lama, $oss->file_izin_lama);
            }

            if ($request->file('file_peta_polygon') && $request->file('file_peta_polygon')->isValid()) {
                $originalName = $no_registrasi.'~'.$request->file('file_peta_polygon')->getClientOriginalName();
                $filename_file_peta_polygon = Storage::disk('oss-doc')->putFileAs(
                    $path_file_peta_polygon,
                    $request->file('file_peta_polygon'),
                    $originalName
                );
                Storage::disk("oss-doc")->delete($path_file_peta_polygon, $oss->file_peta_polygon);
            }

            if ($request->file('rencana_teknis_bangunan') && $request->file('rencana_teknis_bangunan')->isValid()) {
                $originalName = $no_registrasi.'~'.$request->file('rencana_teknis_bangunan')->getClientOriginalName();
                $filename_rencana_teknis_bangunan = Storage::disk('oss-doc')->putFileAs(
                    $path_rencana_teknis_bangunan,
                    $request->file('rencana_teknis_bangunan'),
                    $originalName
                );
                Storage::disk("oss-doc")->delete($path_rencana_teknis_bangunan, $oss->rencana_teknis_bangunan);
            }

            DB::transaction(function () use ($oss, $no_registrasi, $request, $filename_bukti_bayar, $filename_file_izin_lama, $filename_file_peta_polygon, $filename_rencana_teknis_bangunan,
                $path_ms_file_lampiran, $path_sw_file_lampiran, $path_pp_file_lampiran,
                $path_imb_file_lampiran, $path_slf_file_lampiran, $path_rencana_teknis_bangunan,
                $path_file_lampiran_kkpr, $path_amdal_file_lampiran, $path_uklupl_lampiran) {

                $filename_ms_file_lampiran = $oss->ms_file_lampiran;
                $filename_sw_file_lampiran = $oss->sw_file_lampiran;
                $filename_pp_file_lampiran = $oss->pp_file_lampiran;
                $filename_imb_file_lampiran = $oss->imb_file_lampiran;
                $filename_slf_file_lampiran = $oss->slf_file_lampiran;
                $filename_file_lampiran_kkpr = $oss->file_lampiran_kkpr;
                $filename_amdal_file_lampiran = $oss->amdal_file_lampiran;
                $filename_uklupl_lampiran = $oss->uklupl_file_lampiran;

                $dataUpdate = [
                    'email' => $request->email,
                    'npwp' => $request->npwp,
                    'no_whatsapp' => $request->no_whatsapp,
                    'intansi_izin_lama' => $request->intansi_izin_lama,
                    'nomor_izin_lama' => $request->nomor_izin_lama,
                    'tgl_terbit_izin_lama' => $request->tgl_terbit_izin_lama,
                    'tgl_expired_izin_lama' => $request->tgl_expired_izin_lama,
                    'file_izin_lama' => $filename_file_izin_lama,
                    'lokasi_usaha' => $request->lokasi_usaha,
                    'luas_lahan_usaha' => $request->luas_lahan_usaha,
                    'apakah_sudah_menempati_lahan' => $request->apakah_sudah_menempati_lahan,
                    'status_lahan' => $request->status_lahan,
                    'apakah_memerlukan_bangunan_baru' => $request->apakah_memerlukan_bangunan_baru,
                    'sudah_ada_bangunan' => $request->sudah_ada_bangunan,
                    'status_bangunan_usaha' => $request->status_bangunan_usaha,
                    'jumlah_bangunan' => $request->jumlah_bangunan,
                    'apakah_memiliki_imb' => $request->apakah_memiliki_imb,
                    'apakah_memiliki_sertifikat_slf' => $request->apakah_memiliki_sertifikat_slf,
                    'apakah_lokasi_sekolah_lintas_perbatasan' => $request->apakah_lokasi_sekolah_lintas_perbatasan,
                    'alamat_sekolah' => $request->alamat_sekolah,
                    'propinsi' => $request->propinsi,
                    'kabupaten' => $request->kabupaten,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'kode_pos' => $request->kode_pos,
                    'file_peta_polygon' => $filename_file_peta_polygon,
                    'apakah_proyek_strategi_nasional' => $request->apakah_proyek_strategi_nasional,
                    'kawasan_lokasi_usaha' => $request->kawasan_lokasi_usaha,
                    'apakah_memiliki_kkpr' => $request->apakah_memiliki_kkpr,
                    'dri_pembelian_tanah' => $request->dri_pembelian_tanah,
                    'dri_bangunan' => $request->dri_bangunan,
                    'dri_mesin_dalam_negeri' => $request->dri_mesin_dalam_negeri,
                    'dri_mesin_impor' => $request->dri_mesin_impor,
                    'dri_investasi' => $request->dri_investasi,
                    'dri_modal_kerja_3_bulan' => $request->dri_modal_kerja_3_bulan,
                    'tgl_mulai_beroperasi' => $request->tgl_mulai_beroperasi,
                    'jml_pegawai_pria' => $request->jml_pegawai_pria,
                    'jml_pegawai_wanita' => $request->jml_pegawai_wanita,
                    'jml_pegawai_asing' => $request->jml_pegawai_asing,
                    'apakah_memiliki_izin_amdal' => $request->apakah_memiliki_izin_amdal,
                    'apakah_memiliki_uklupl' => $request->apakah_memiliki_uklupl,
                    'rencana_teknis_bangunan' => $filename_rencana_teknis_bangunan,

                    'bukti_bayar' => $filename_bukti_bayar,
                    'tanggal' => Carbon::now(),
                    'status' => 'verifikasi',
                ];

                /** Status Lahan */
                if ($request->status_lahan == "Milik Sendiri") {
                    if ($request->file('ms_file_lampiran') && $request->file('ms_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('ms_file_lampiran')->getClientOriginalName();
                        $filename_ms_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_ms_file_lampiran,
                            $request->file('ms_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_ms_file_lampiran, $oss->ms_file_lampiran);
                    }

                    $dataUpdate = array_merge($dataUpdate, [
                        'ms_instansi_izin' => $request->ms_instansi_izin,
                        'ms_nomor_izin' => $request->ms_nomor_izin,
                        'ms_tgl_terbit' => $request->ms_tgl_terbit,
                        'ms_tgl_expired' => $request->ms_tgl_expired,
                        'ms_file_lampiran' => $filename_ms_file_lampiran,
                    ]);

                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'ms_instansi_izin' => '',
                        'ms_nomor_izin' => '',
                        'ms_tgl_terbit' => null,
                        'ms_tgl_expired' => null,
                        'ms_file_lampiran' => '',
                    ]);
                }
                if ($request->status_lahan == "Sewa") {
                    if ($request->file('sw_file_lampiran') && $request->file('sw_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('sw_file_lampiran')->getClientOriginalName();
                        $filename_sw_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_sw_file_lampiran,
                            $request->file('sw_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_sw_file_lampiran, $oss->sw_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'sw_pemilik_lahan' => $request->sw_pemilik_lahan,
                        'sw_nomor_perjanjian' => $request->sw_nomor_perjanjian,
                        'sw_tgl_perjanjian' => $request->sw_tgl_perjanjian,
                        'sw_tgl_expired' => $request->sw_tgl_expired,
                        'sw_file_lampiran' => $filename_sw_file_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'sw_pemilik_lahan' => '',
                        'sw_nomor_perjanjian' => '',
                        'sw_tgl_perjanjian' => null,
                        'sw_tgl_expired' => null,
                        'sw_file_lampiran' => '',
                    ]);
                }

                if ($request->status_lahan == "Pinjam Pakai") {
                    if ($request->file('pp_file_lampiran') && $request->file('pp_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('pp_file_lampiran')->getClientOriginalName();
                        $filename_pp_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_pp_file_lampiran,
                            $request->file('pp_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_pp_file_lampiran, $oss->pp_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'pp_pemilik_lahan' => $request->pp_pemilik_lahan,
                        'pp_nomor_perjanjian' => $request->pp_nomor_perjanjian,
                        'pp_tgl_perjanjian' => $request->pp_tgl_perjanjian,
                        'pp_tgl_expired' => $request->pp_tgl_expired,
                        'pp_file_lampiran' => $filename_pp_file_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'pp_pemilik_lahan' => '',
                        'pp_nomor_perjanjian' => '',
                        'pp_tgl_perjanjian' => null,
                        'pp_tgl_expired' => null,
                        'pp_file_lampiran' => '',
                    ]);
                }

                /** Pertanyaan IMB */
                if ($request->apakah_memiliki_imb == "Iya") {
                    if ($request->file('imb_file_lampiran') && $request->file('imb_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('imb_file_lampiran')->getClientOriginalName();
                        $filename_imb_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_imb_file_lampiran,
                            $request->file('imb_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_imb_file_lampiran, $oss->imb_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'imb_jml_bangunan' => $request->imb_jml_bangunan,
                        'imb_pejabat_penerbit_izin' => $request->imb_pejabat_penerbit_izin,
                        'imb_nomor' => $request->imb_nomor,
                        'imb_tgl_terbit' => $request->imb_tgl_terbit,
                        'imb_tgl_expired' => $request->imb_tgl_expired,
                        'imb_file_lampiran' => $filename_imb_file_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'imb_jml_bangunan' => null,
                        'imb_pejabat_penerbit_izin' => '',
                        'imb_nomor' => '',
                        'imb_tgl_terbit' => null,
                        'imb_tgl_expired' => null,
                        'imb_file_lampiran' => '',
                    ]);
                }

                /** Pertanyaan SLF */
                if ($request->apakah_memiliki_sertifikat_slf == "Iya") {
                    if ($request->file('slf_file_lampiran') && $request->file('slf_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('slf_file_lampiran')->getClientOriginalName();
                        $filename_slf_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_slf_file_lampiran,
                            $request->file('slf_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_slf_file_lampiran, $oss->slf_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'slf_pejabat_penerbit' => $request->slf_pejabat_penerbit,
                        'slf_nomor' => $request->slf_nomor,
                        'slf_tgl_terbit' => $request->slf_tgl_terbit,
                        'slf_tgl_expired' => $request->slf_tgl_expired,
                        'slf_file_lampiran' => $filename_slf_file_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'slf_pejabat_penerbit' => '',
                        'slf_nomor' => '',
                        'slf_tgl_terbit' => null,
                        'slf_tgl_expired' => null,
                        'slf_file_lampiran' => '',
                    ]);
                }

                if ($request->kawasan_lokasi_usaha == "Didalam Kawasan") {
                    $dataUpdate = array_merge($dataUpdate, [
                        'klu_nama_kawasan_industri' => $request->klu_nama_kawasan_industri,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'klu_nama_kawasan_industri' => $oss->klu_nama_kawasan_industri,
                    ]);
                }

                /** Pertanyaan KKPR */
                if ($request->apakah_memiliki_kkpr == "Iya") {
                    if ($request->file('file_lampiran_kkpr') && $request->file('file_lampiran_kkpr')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('file_lampiran_kkpr')->getClientOriginalName();
                        $filename_file_lampiran_kkpr = Storage::disk('oss-doc')->putFileAs(
                            $path_file_lampiran_kkpr,
                            $request->file('file_lampiran_kkpr'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_file_lampiran_kkpr, $oss->file_lampiran_kkpr);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'pejabat_penerbit_kkpr' => $request->pejabat_penerbit_kkpr,
                        'nomor_kkpr' => $request->nomor_kkpr,
                        'tgl_terbit_kkpr' => $request->tgl_terbit_kkpr,
                        'tgl_expired_kkpr' => $request->tgl_expired_kkpr,
                        'file_lampiran_kkpr' => $filename_file_lampiran_kkpr,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'pejabat_penerbit_kkpr' => '',
                        'nomor_kkpr' => '',
                        'tgl_terbit_kkpr' => null,
                        'tgl_expired_kkpr' => null,
                        'file_lampiran_kkpr' => '',
                    ]);
                }

                /** Pertanyaan AMDAL */
                if ($request->apakah_memiliki_izin_amdal == "Iya") {
                    if ($request->file('amdal_file_lampiran') && $request->file('amdal_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('amdal_file_lampiran')->getClientOriginalName();
                        $filename_amdal_file_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_amdal_file_lampiran,
                            $request->file('amdal_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_amdal_file_lampiran, $oss->amdal_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'amdal_pejabat_penerbit' => $request->amdal_pejabat_penerbit,
                        'amdal_nomor_izin' => $request->amdal_nomor_izin,
                        'amdal_tgl_terbit' => $request->amdal_tgl_terbit,
                        'amdal_tgl_expired' => $request->amdal_tgl_expired,
                        'amdal_file_lampiran' => $filename_amdal_file_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'amdal_pejabat_penerbit' => '',
                        'amdal_nomor_izin' => '',
                        'amdal_tgl_terbit' => null,
                        'amdal_tgl_expired' => null,
                        'amdal_file_lampiran' => '',
                    ]);
                }

                /** Pertanyaan UKLUPL */
                if ($request->apakah_memiliki_uklupl == "Iya") {
                    if ($request->file('uklupl_file_lampiran') && $request->file('uklupl_file_lampiran')->isValid()) {
                        $originalName = $no_registrasi.'~'.$request->file('uklupl_file_lampiran')->getClientOriginalName();
                        $filename_uklupl_lampiran = Storage::disk('oss-doc')->putFileAs(
                            $path_uklupl_lampiran,
                            $request->file('uklupl_file_lampiran'),
                            $originalName
                        );
                        Storage::disk("oss-doc")->delete($path_uklupl_lampiran, $oss->uklupl_file_lampiran);
                    }
                    $dataUpdate = array_merge($dataUpdate, [
                        'uklupl_pejabat_penerbit' => $request->uklupl_pejabat_penerbit,
                        'uklupl_nomor_izin' => $request->uklupl_nomor_izin,
                        'uklupl_tgl_terbit' => $request->uklupl_tgl_terbit,
                        'uklupl_tgl_expired' => $request->uklupl_tgl_expired,
                        'uklupl_file_lampiran' => $filename_uklupl_lampiran,
                    ]);
                } else {
                    $dataUpdate = array_merge($dataUpdate, [
                        'uklupl_pejabat_penerbit' => '',
                        'uklupl_nomor_izin' => '',
                        'uklupl_tgl_terbit' => null,
                        'uklupl_tgl_expired' => null,
                        'uklupl_file_lampiran' => '',
                    ]);
                }

                /** Push Oss Update */
                $oss->update($dataUpdate);

                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'verifikasi',
                ])->update([
                    'status' => 'success',
                ]);

                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'perbaikan',
                ])->update([
                    'status' => null,
                ]);

                OSSTimeline::create([
                    'id_oss' => $oss->id_oss,
                    'status_verifikasi' => 'verifikasi',
                    'tgl_verifikasi' => Carbon::now(),
                    'catatan' => 'permohonan verifikasi pengajuan oss',
                    'link_pnbp' => null,
                    'link_catatan_pupr' => null,
                    'link_gistaru' => null,
                    'link_izin_terbit' => null,
                    'nomor_ku' => null,
                ]);

            });

            return redirect()->back()->with('success', 'Berhasil melakukan permohonan OSS');

        } catch (\Exception $e) {
            if ($oss->status != "perbaikan") {
                Storage::disk("oss-doc")->delete($path_bukti_bayar, $filename_bukti_bayar);
                Storage::disk("oss-doc")->delete($path_file_izin_lama, $filename_file_izin_lama);
                Storage::disk("oss-doc")->delete($path_file_peta_polygon, $filename_file_peta_polygon);
                Storage::disk("oss-doc")->delete($path_rencana_teknis_bangunan, $filename_rencana_teknis_bangunan);
            }
            return redirect()->back()->with('error', 'Gagal melakukan permohonan OSS ' . $e->getMessage());
        }
    }

    public function historyOSSRequest() {
        $ossHistory = OSS::with(["ossstatus", "satpen.kabupaten:id_kab,nama_kab","osstimeline" => function ($query) {
                $query->where('status_verifikasi', '=', 'izin terbit')
                    ->orderBy('id_timeline', 'DESC');
            }])->where('id_user', '=', auth()->user()->id_user)
            ->where('status', '=', 'izin terbit')
            ->orderBy('id_oss', 'DESC')
            ->get();

        return view('oss.history', compact('ossHistory'));
    }

    public function forbiddenPage() {
        return view('oss.forbidden');
    }
}
