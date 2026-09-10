<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoretaxRequest;
use App\Models\Coretax;
use App\Models\CoretaxStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CoretaxController extends Controller
{
    public function index()
    {
        $coretax = Coretax::with(["satpen:id_satpen,id_user,no_registrasi,nm_satpen", "corestatus", "wilayah"])
            ->where('id_user', '=', auth()->user()->id_user)
            ->orderBy('id', 'DESC')
            ->first();

        $notice = null;
        if ($coretax) {
            $notice = CoretaxStatus::where([
                'id_coretax' => $coretax->id,
                'statusType' => $coretax->status,
            ])->first();
        }
        return view('coretax.pengajuan', compact('coretax', 'notice'));
    }


    public function new()
    {
        $lastCoretax = Coretax::where('id_user', '=', auth()->user()->id_user)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastCoretax) {
            $expiryDate = Carbon::parse($lastCoretax->tgl_expiry);
            if (!$expiryDate->isToday() || !$expiryDate->isPast()) {

                return redirect()->back()->with(
                    'error',
                    'Tidak dapat mengajukan layanan Coretax, pengajuan sebelumnya belum expired! ' .
                        '<a href="' . route('coretax.req-exipry') . '" class="btn btn-sm btn-primary">Buka Expiry</a>'
                );
            }

            $lastCoretax->new_request = null;
            $lastCoretax->save();
        }

        $data = [
            'id_user' => auth()->user()->id_user,
            'id_pw' => null,
            'id_pc' => null,
            'nitku' => optional($lastCoretax)->nitku,
            'nama_pic' => optional($lastCoretax)->nama_pic,
            'nik_pic' => optional($lastCoretax)->nik_pic,
            'whatsapp_pic' => optional($lastCoretax)->whatsapp_pic,
            'npwp_lama' => optional($lastCoretax)->npwp_lama,
            'tanggal' => Carbon::now(),
            'tgl_submit' => null,
            'status' => 'mengisi persyaratan',
        ];

        if (auth()->user()->provId && auth()->user()->cabangId) {
            $data['id_pc'] = auth()->user()->cabangId;
        } elseif (auth()->user()->provId) {
            $data['id_pw'] = auth()->user()->provId;
        }

        $coretax = Coretax::create($data);
        CoretaxStatus::insert([
            [
                'id_coretax' => $coretax->id,
                'statusType' => 'mengisi persyaratan',
                'icon' => 'ti-keyboard',
                'textstatus' => 'Mengisi Persyaratan',
                'status' => 'success',
            ],
            [
                'id_coretax' => $coretax->id,
                'statusType' => 'verifikasi',
                'icon' => 'ti-building-bank',
                'textstatus' => 'Verifikasi',
                'status' => null,
            ],
            [
                'id_coretax' => $coretax->id,
                'statusType' => 'perbaikan',
                'icon' => 'ti-checkup-list',
                'textstatus' => 'Diterima Verifikator',
                'status' => null,
            ],
            [
                'id_coretax' => $coretax->id,
                'statusType' => 'dokumen diproses',
                'icon' => 'ti-manual-gearbox',
                'textstatus' => 'Sedang Diproses',
                'status' => null,
            ],
            [
                'id_coretax' => $coretax->id,
                'statusType' => 'final aprove',
                'icon' => 'ti-fingerprint',
                'textstatus' => 'Final Aprove',
                'status' => null,
            ],
        ]);

        return redirect()->back();
    }

    public function openExpiry()
    {
        $coretax = Coretax::where('id_user', auth()->user()->id_user)
            ->orderBy('id', 'DESC')
            ->first();

        if ($coretax) {
            $coretax->new_request = 1;
            $coretax->save();
        }
        return redirect()->back()->with('success', 'Permohonan hapus expiry berhasil dilakukan. Silahkan menunggu verifikasi Admin.');
    }

    public function stored(CoretaxRequest $request, Coretax $coretax)
    {
        $pathNpwpLama = $coretax->npwp_lama;
        if ($request->file('npwp_lama')
            && $request->file('npwp_lama')->isValid()) {
            $pathNpwpLama = Storage::disk('coretax-doc')->putFile("npwp-lama", $request->file('npwp_lama'));
            Storage::disk("coretax-doc")->delete("npwp-lama", "npwp-lama/".$coretax->npwp_lama);
        }

        $coretax->update([
            'nitku' => $request->nitku,
            'nama_pic' => $request->nm_pic,
            'nik_pic' => $request->nik_pic,
            'whatsapp_pic' => $request->whatsapp_pic,
            'tgl_submit' => Carbon::now(),
            'status' => 'verifikasi',
            'npwp_lama' => basename($pathNpwpLama),
        ]);

        CoretaxStatus::where([
            'id_coretax' => $coretax->id,
            'statusType' => 'verifikasi',
        ])->update([
            'status' => 'success',
        ]);

        CoretaxStatus::where([
            'id_coretax' => $coretax->id,
            'statusType' => 'perbaikan',
        ])->update([
            'status' => null,
            'keterangan' => null,
        ]);

        return redirect()->back()->with('success', 'Berhasil mengajukan permohonan layanan Coretax');
    }

    public function history()
    {
        $coretax = Coretax::with(["corestatus"])
            ->where('id_user', '=', auth()->user()->id_user)
            ->where('status', '=', 'final aprove')
            ->orderBy('id', 'DESC')
            ->get();

        return view('coretax.riwayat', compact('coretax'));
    }

    public function forbidden()
    {
        return view('coretax.forbidden');
    }
}
