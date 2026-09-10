<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\OSS;
use App\Models\OSSStatus;
use App\Models\OSSTimeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OSSController extends Controller
{
    public function listPermohonanOSS() {

        $specificFilter = request()->specificFilter;

        $ossVerifikasi = OSS::with(["satpen:id_satpen,id_user,id_kab,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab","osstimeline" => function ($query) {
                $query->where('status_verifikasi', '=', 'verifikasi')
                    ->orderBy('id_timeline', 'DESC');
            }])->where('status', '=', 'verifikasi')
            ->whereHas('satpen', function($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_oss', 'DESC') ->get();

        $ossRevisi = OSS::with(["satpen:id_satpen,id_user,id_kab,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab","osstimeline" => function ($query) {
                $query->where('status_verifikasi', '=', 'perbaikan')
                    ->orderBy('id_timeline', 'DESC');
            }])->where('status', '=','perbaikan')
            ->whereHas('satpen', function($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_oss', 'DESC') ->get();

        $ossProses = OSS::with(["satpen:id_satpen,id_user,id_kab,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab","osstimeline" => function ($query) {
                $query->where('status_verifikasi', '=', 'dokumen diproses')
                    ->orderBy('id_timeline', 'DESC');
            }])->where('status', '=', 'dokumen diproses')
            ->whereHas('satpen', function($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_oss', 'DESC')->get();

        $ossTerbit = OSS::with(["satpen:id_satpen,id_user,id_kab,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab","osstimeline" => function ($query) {
                $query->where('status_verifikasi', '=', 'izin terbit')
                    ->orderBy('id_timeline', 'DESC');
            }])->where('status', '=', 'izin terbit')
            ->whereHas('satpen', function($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_oss', 'DESC')->get();

        return view('admin.oss.oss', compact('ossVerifikasi', 'ossRevisi', 'ossProses', 'ossTerbit'));
    }

    public function detailOSSQuesioner(string $ossId) {
        $specificFilter = request()->specificFilter;

        $oss = OSS::with(["satpen:id_satpen,id_user,no_registrasi,nm_satpen"])->where('id_oss', '=', $ossId)
            ->whereHas('satpen', function($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_oss', 'DESC') ->first();

        return view('admin.oss.oss-detail', compact('oss'));
    }

    public function setAcceptOSS(Request $request, OSS $oss) {

        try {
            if ($oss) {
                $oss->update([
                    'status' => 'dokumen diproses',
                ]);
                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Diterima Verifikator',
                    'status' => 'success',
                ]);
                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'dokumen diproses',
                ])->update([
                    'status' => 'success',
                    'keterangan' => null,
                ]);

                OSSTimeline::create([
                    'id_oss' => $oss->id_oss,
                    'status_verifikasi' => 'dokumen diproses',
                    'tgl_verifikasi' => Carbon::now(),
                    'catatan' => $request->catatan,
                    'link_pnbp' => $request->link_pnbp,
                    'link_catatan_pupr' => $request->link_catatan_pupr,
                    'link_gistaru' => $request->link_gistaru,
                    'link_izin_terbit' => $request->link_izin_terbit,
                    'nomor_ku' => $request->nomor_ku,
                ]);

                return redirect()->back()->with('success', 'Berhasil menerima permohonan');
            }
            return redirect()->back()->with('error', 'Invalid OSS Id');

        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function setRejectOSS(Request $request, OSS $oss) {

        try {
            if ($oss) {
                $oss->update([
                    'status' => 'perbaikan',
                ]);
                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Ditolak Verifikator',
                    'status' => 'failed',
                    'keterangan' => $request->keterangan,
                ]);
                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'dokumen diproses',
                ])->update([
                    'status' => null,
                    'keterangan' => null
                ]);

                OSSTimeline::create([
                    'id_oss' => $oss->id_oss,
                    'status_verifikasi' => 'perbaikan',
                    'tgl_verifikasi' => Carbon::now(),
                    'catatan' => $request->catatan,
                    'link_pnbp' => $request->link_pnbp,
                    'link_catatan_pupr' => $request->link_catatan_pupr,
                    'link_gistaru' => $request->link_gistaru,
                    'link_izin_terbit' => $request->link_izin_terbit,
                    'nomor_ku' => $request->nomor_ku,
                ]);

                return redirect()->back()->with('success', 'Permohonan oss ditolak');
            }
            return redirect()->back()->with('error', 'Invalid OSS Id');

        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function setIzinTerbitOSS(Request $request, OSS $oss) {

        try {
            $request->validate([
                'tgl_expired' => 'required',
            ]);

            if ($oss) {
                $oss->update([
                    'tgl_izin' => Carbon::now(),
                    'tgl_expired' => $request->tgl_expired,
                    'status' => 'izin terbit',
                ]);
                OSSStatus::where([
                    'id_oss' => $oss->id_oss,
                    'statusType' => 'izin terbit',
                ])->update([
                    'status' => 'success',
                    'keterangan' => null,
                ]);

                OSSTimeline::create([
                    'id_oss' => $oss->id_oss,
                    'status_verifikasi' => 'izin terbit',
                    'tgl_verifikasi' => Carbon::now(),
                    'catatan' => $request->catatan,
                    'link_pnbp' => $request->link_pnbp,
                    'link_catatan_pupr' => $request->link_catatan_pupr,
                    'link_gistaru' => $request->link_gistaru,
                    'link_izin_terbit' => $request->link_izin_terbit,
                    'nomor_ku' => $request->nomor_ku,
                ]);

                return redirect()->back()->with('success', 'Berhasil menerbitkan izin oss');
            }
            return redirect()->back()->with('error', 'Invalid OSS Id');

        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function destroyOSS(OSS $oss) {
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
        try {
            if ($oss) {
                Storage::disk("oss-doc")->delete($path_bukti_bayar, $oss->bukti_bayar);
                Storage::disk("oss-doc")->delete($path_file_izin_lama, $oss->file_izin_lama);
                Storage::disk("oss-doc")->delete($path_file_peta_polygon, $oss->file_peta_polygon);
                Storage::disk("oss-doc")->delete($path_rencana_teknis_bangunan, $oss->rencana_teknis_bangunan);
                Storage::disk("oss-doc")->delete($path_ms_file_lampiran, $oss->ms_file_lampiran);
                Storage::disk("oss-doc")->delete($path_sw_file_lampiran, $oss->sw_file_lampiran);
                Storage::disk("oss-doc")->delete($path_pp_file_lampiran, $oss->pp_file_lampiran);
                Storage::disk("oss-doc")->delete($path_imb_file_lampiran, $oss->imb_file_lampiran);
                Storage::disk("oss-doc")->delete($path_slf_file_lampiran, $oss->slf_file_lampiran);
                Storage::disk("oss-doc")->delete($path_file_lampiran_kkpr, $oss->file_lampiran_kkpr);
                Storage::disk("oss-doc")->delete($path_amdal_file_lampiran, $oss->amdal_file_lampiran);
                Storage::disk("oss-doc")->delete($path_uklupl_lampiran, $oss->uklupl_file_lampiran);
                $oss->delete();

                return redirect()->back()->with('success', 'Berhasil menghapus izin OSS');
            }
            return redirect()->back()->with('error', 'Invalid OSS Id');

        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }
}
