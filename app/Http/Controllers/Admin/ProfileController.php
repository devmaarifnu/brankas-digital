<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\PengurusCabang;
use App\Models\ProfilePengurusCabang;
use App\Models\ProfilePengurusWilayah;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profileCabang(Request $request)
    {
        $specificFilter = request()->specificFilter;

        if ($request->wilayah && !in_array(auth()->user()->role, ['admin wilayah'])) {
            $wilayah = $request->get('wilayah');
            $cabang = PengurusCabang::with(["profile", "prov"])
                ->orderBy('id_pc', 'DESC')
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })
                ->whereHas("prov", function ($query) use ($wilayah) {
                    $query->where('id_prov', $wilayah);
                })
                ->get();
        } else {
            $cabang = PengurusCabang::with(["profile", "prov"])
                ->orderBy('id_pc', 'DESC')
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })
                ->whereHas("prov", function ($query) use ($specificFilter) {
                    $query->where($specificFilter);
                })
                ->get();
        }

        $prov = Provinsi::orderBy('id_prov', 'ASC')->get();
        return view('admin.profile.profile-cabang', compact('cabang', 'prov'));
    }

    public function profileWilayah()
    {
        $wilayah = Provinsi::with("profile")
            ->orderBy('id_prov', 'DESC')
            ->whereHas("profile", function ($query) {
                $query->where('id', '!=', null);
            })
            ->get();
        return view('admin.profile.profile-wilayah', compact('wilayah'));
    }

    public function profileDetail($ID)
    {
        if ($ID) {
            if (request()->segment(3) === "cabang") {
                $specificFilter = request()->specificFilter;

                $data = PengurusCabang::with(["profile", "prov"])
                    ->whereHas("profile", function ($query) {
                        $query->where('id', '!=', null);
                    })
                    ->whereHas("prov", function ($query) use ($specificFilter) {
                        $query->where($specificFilter);
                    })
                    ->where("id_pc", $ID)
                    ->first();
                return view('admin.profile.detail-profile', compact('data'));
            }
            $data = Provinsi::with("profile")
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })
                ->where("id_prov", $ID)
                ->first();
            return view('admin.profile.detail-profile', compact('data'));
        }
    }

    public function destroyWilayah(ProfilePengurusWilayah $profile)
    {
        try {
            if ($profile) {
                $profile->delete();
                return redirect()->back()->with('success', 'Berhasil menghapus profile organisasi');
            }
            return redirect()->back()->with('error', 'Invalid Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function destroyCabang(ProfilePengurusCabang $profile)
    {
        try {
            if ($profile) {
                $profile->delete();
                return redirect()->back()->with('success', 'Berhasil menghapus profile organisasi');
            }
            return redirect()->back()->with('error', 'Invalid Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }
}
