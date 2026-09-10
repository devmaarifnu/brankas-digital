<?php

namespace App\Http\Controllers;

use App\Http\Requests\BHPNURequest;
use App\Models\BHPNU;
use App\Models\BHPNUStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BHPNUController extends Controller
{
    public function permohonanBHPNUPage() {

        $bhpnu = BHPNU::with("bhpnustatus")
            ->where('id_user', '=', auth()->user()->id_user)
            ->orderBy('id_bhpnu', 'DESC')
            ->first();

        $notice=null;
        if ($bhpnu) {
            $notice = BHPNUStatus::where([
                'id_bhpnu' => $bhpnu->id_bhpnu,
                'statusType' => $bhpnu->status,
            ])->first();
        }
        return view('bhpnu.permohonan', compact('bhpnu', 'notice'));
    }

    public function permohonanBaruBHPNU() {

        $bhpnu = BHPNU::create([
            'id_user' => auth()->user()->id_user,
            'kode_unik' => '',
            'bukti_bayar' => '',
            'tanggal' => Carbon::now(),
            'status' => 'mengisi persyaratan',
        ]);

        BHPNUStatus::insert([
           [
               'id_bhpnu' => $bhpnu->id_bhpnu,
               'statusType' => 'mengisi persyaratan',
               'icon' => 'ti-keyboard',
               'textstatus' => 'Mengisi Persyaratan',
               'status' => 'success',
           ],[
               'id_bhpnu' => $bhpnu->id_bhpnu,
               'statusType' => 'verifikasi',
               'icon' => 'ti-building-bank',
               'textstatus' => 'Verifikasi',
               'status' => null,
           ],[
               'id_bhpnu' => $bhpnu->id_bhpnu,
               'statusType' => 'perbaikan',
               'icon' => 'ti-checkup-list',
               'textstatus' => 'Diterima Verifikator',
               'status' => null,
           ],[
               'id_bhpnu' => $bhpnu->id_bhpnu,
               'statusType' => 'dokumen diproses',
               'icon' => 'ti-manual-gearbox',
               'textstatus' => 'Sedang Diproses',
               'status' => null,
           ],[
               'id_bhpnu' => $bhpnu->id_bhpnu,
               'statusType' => 'dokumen dikirim',
               'icon' => 'ti-fingerprint',
               'textstatus' => 'Dokumen Dikirim',
               'status' => null,
           ],
        ]);

        return redirect()->back();

    }

    public function storePermohonanBHPNU(BHPNURequest $request, BHPNU $bhpnu) {

        $request->validate([
            'bukti_bayar' => 'required|nullable|file|mimes:pdf,jpg,jpeg,png,svg|max:1024',
        ]);

        if ($request->file('bukti_bayar')
            && $request->file('bukti_bayar')->isValid()) {
            $pathBuktiBayar = Storage::disk('bhpnu-doc')->putFile("bukti-bayar", $request->file('bukti_bayar'));
            Storage::disk("bhpnu-doc")->delete("bukti-bayar", "bukti-bayar/".$bhpnu->bukti_bayar);
        }

        $bhpnu->update([
            'bukti_bayar' => basename($pathBuktiBayar),
            'tanggal' => Carbon::now(),
            'status' => 'verifikasi',
        ]);

        BHPNUStatus::where([
            'id_bhpnu' => $bhpnu->id_bhpnu,
            'statusType' => 'verifikasi',
        ])->update([
            'status' => 'success',
        ]);

        BHPNUStatus::where([
            'id_bhpnu' => $bhpnu->id_bhpnu,
            'statusType' => 'perbaikan',
        ])->update([
            'status' => null,
            'keterangan' => null,
        ]);

        return redirect()->back()->with('success', 'Berhasil melakukan permohonan BHPNU');
    }

    public function historyPermohonan() {
        $bhpnuHistory = BHPNU::with("bhpnustatus")
            ->where('id_user', '=', auth()->user()->id_user)
            ->where('status', '=', 'dokumen dikirim')
            ->orderBy('id_bhpnu', 'DESC')
            ->get();

        return view('bhpnu.history', compact('bhpnuHistory'));
    }

    public function forbiddenPage() {
        return view('bhpnu.forbidden');
    }
}
