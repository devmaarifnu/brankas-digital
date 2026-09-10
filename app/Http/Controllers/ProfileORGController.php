<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileORGRequest;
use App\Models\PengurusCabang;
use App\Models\ProfilePengurusCabang;
use App\Models\ProfilePengurusWilayah;
use App\Models\Provinsi;

class ProfileORGController extends Controller
{
    public function index() {
        if (auth()->user()->cabangId) {
            $data = PengurusCabang::with(["profile", "prov"])
            ->where('id_pc', auth()->user()->cabangId)
            ->first();
            return view('home.profile-org-cabang', compact('data'));
        }
        $data = Provinsi::with("profile")
            ->where('id_prov', auth()->user()->provId)
            ->first();
        return view('home.profile-organisasi', compact('data'));
    }

    
    public function storeOrUpdate(ProfileORGRequest $request)
    {
        $data = $request->validated();
        if (auth()->user()->cabangId) {
            ProfilePengurusCabang::updateOrCreate(
                ['id_pc' => $data['id_pc']],
                $data
            );

        } else {
            ProfilePengurusWilayah::updateOrCreate(
                ['id_pw' => $data['id_pw']],
                $data
            );
        }
        return redirect()->back()->with('success', 'Profile organisasi berhasil disimpan');
    }


}
