<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\BHPNU;
use App\Models\BHPNUStatus;
use Illuminate\Http\Request;

class BHPNUController extends Controller
{
    public function listPermohonanBHPNU()
    {

        $specificFilter = request()->specificFilter;

        $bhpnuVerifikasi = BHPNU::with([
            "satpen:id_satpen,id_user,no_registrasi,nm_satpen,id_prov,id_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "satpen.kabupaten:id_kab,nama_kab",
        ])->where('status', '=', 'verifikasi')
            ->whereHas('satpen', function ($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_bhpnu', 'DESC')->get();

        $bhpnuProses = BHPNU::with([
            "satpen:id_satpen,id_user,no_registrasi,nm_satpen,id_prov,id_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "satpen.kabupaten:id_kab,nama_kab"
        ])->where('status', '=', 'dokumen diproses')
            ->whereHas('satpen', function ($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_bhpnu', 'DESC')->get();
            
        $bhpnuDikirim = BHPNU::with([
            "satpen:id_satpen,id_user,no_registrasi,nm_satpen,id_prov,id_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "satpen.kabupaten:id_kab,nama_kab"
        ])->where('status', '=', 'dokumen dikirim')
            ->whereHas('satpen', function ($query) use ($specificFilter) {
                $query->where($specificFilter);
            })->orderBy('id_bhpnu', 'DESC')->get();

        return view('admin.bhpnu.bhpnu', compact('bhpnuVerifikasi', 'bhpnuProses', 'bhpnuDikirim'));
    }

    public function setAcceptBHPNU(BHPNU $bhpnu)
    {

        try {
            if ($bhpnu) {
                $bhpnu->update([
                    'status' => 'dokumen diproses',
                ]);
                BHPNUStatus::where([
                    'id_bhpnu' => $bhpnu->id_bhpnu,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Diterima Verifikator',
                    'status' => 'success',
                ]);
                BHPNUStatus::where([
                    'id_bhpnu' => $bhpnu->id_bhpnu,
                    'statusType' => 'dokumen diproses',
                ])->update([
                    'status' => 'success',
                ]);

                return redirect()->back()->with('success', 'Berhasil menerima permohonan');
            }
            return redirect()->back()->with('error', 'Invalid BHPNU Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function setRejectBHPNU(Request $request, BHPNU $bhpnu)
    {

        try {
            if ($bhpnu) {
                $bhpnu->update([
                    'status' => 'perbaikan',
                ]);
                BHPNUStatus::where([
                    'id_bhpnu' => $bhpnu->id_bhpnu,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Ditolak Verifikator',
                    'status' => 'failed',
                    'keterangan' => $request->keterangan,
                ]);

                return redirect()->back()->with('success', 'Permohonan bhpnu ditolak');
            }
            return redirect()->back()->with('error', 'Invalid BHPNU Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function setIzinTerbitBHPNU(Request $request, BHPNU $bhpnu)
    {

        try {
            if ($bhpnu) {
                $bhpnu->update([
                    'no_resi' => $request->nomor_resi,
                    'tgl_dikirim' => $request->tgl_dikirim,
                    'tgl_expired' => $request->tgl_expired,
                    'status' => 'dokumen dikirim',
                ]);
                BHPNUStatus::where([
                    'id_bhpnu' => $bhpnu->id_bhpnu,
                    'statusType' => 'dokumen dikirim',
                ])->update([
                    'status' => 'success',
                    'keterangan' => $request->keterangan ?? 'Dokumen telah dikirimkan ke alamat anda',
                ]);

                return redirect()->back()->with('success', 'Berhasil menerbitkan izin bhpnu');
            }
            return redirect()->back()->with('error', 'Invalid BHPNU Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function destroyBHPNU(BHPNU $bhpnu)
    {
        try {
            if ($bhpnu) {
                $bhpnu->delete();
                return redirect()->back()->with('success', 'Berhasil menghapus izin BHPNU');
            }
            return redirect()->back()->with('error', 'Invalid BHPNU Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }
}
