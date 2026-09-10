<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\Coretax;
use App\Models\CoretaxStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CoretaxAdminController extends Controller
{
    public function index()
    {

        $specificFilter = request()->specificFilter;

        $coretaxVer = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "wilayah",
            "wilayah.profile:id_pw,kabupaten",
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile:id_pc,kabupaten",
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('cabang.prov', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('wilayah', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->where('status', '=', 'verifikasi')
            ->orderBy('id', 'DESC')->get();

        $coretaxRev = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "wilayah",
            "wilayah.profile:id_pw,kabupaten",
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile:id_pc,kabupaten",
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('cabang.prov', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('wilayah', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->where('status', '=', 'perbaikan')
            ->orderBy('id', 'DESC')->get();

        $coretaxExp = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "wilayah",
            "wilayah.profile:id_pw,kabupaten",
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile:id_pc,kabupaten",
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('cabang.prov', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('wilayah', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->where('status', '=', 'final aprove')
            ->where('new_request', '=', 1)
            ->where('tgl_expiry', '>', Carbon::today())
            ->orderBy('id', 'DESC')->get();

        $coretaxPro = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "wilayah",
            "wilayah.profile:id_pw,kabupaten",
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile:id_pc,kabupaten",
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('cabang.prov', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                })->orWhereHas('wilayah', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->where('status', '=', 'dokumen diproses')
            ->orderBy('id', 'DESC')->get();

        $coretaxSatpen = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
        ])
            ->whereHas('satpen', function ($query) use ($specificFilter) {
                $query->where($specificFilter);
            })
            ->where('status', '=', 'final aprove')
            ->whereNull('id_pw')
            ->whereNull('id_pc')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('coretax')
                    ->where('status', '=', 'final aprove')
                    ->groupBy('id_user');
            })
            ->orderByDesc('tgl_acc')
            ->orderByDesc('id')
            ->get();

        $coretaxWil = Coretax::with([
            "wilayah",
            "wilayah.profile:id_pw,kabupaten",
        ])
            ->where('status', '=', 'final aprove')
            ->whereNotNull('id_pw')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('coretax')
                    ->where('status', '=', 'final aprove')
                    ->groupBy('id_pw');
            })
            ->orderByDesc('tgl_acc')
            ->orderByDesc('id')
            ->get();

        $coretaxCab = Coretax::with([
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile:id_pc,kabupaten",
        ])
            ->whereHas('cabang.prov', function ($query) use ($specificFilter) {
                $query->where($specificFilter);
            })
            ->where('status', '=', 'final aprove')
            ->whereNotNull('id_pc')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('coretax')
                    ->where('status', '=', 'final aprove')
                    ->groupBy('id_pc');
            })
            ->orderByDesc('tgl_acc')
            ->orderByDesc('id')
            ->get();

        return view('admin.coretax.coretax', compact('coretaxVer', 'coretaxExp', 'coretaxRev', 'coretaxPro', 'coretaxSatpen', 'coretaxWil', 'coretaxCab'));
    }

    public function getById($coretaxId)
    {
        $coretax = Coretax::with([
            "satpen:id_satpen,id_user,id_kab,id_prov,no_registrasi,nm_satpen,kecamatan,kelurahan,alamat,kepsek",
            "satpen.kabupaten:id_kab,nama_kab",
            "satpen.provinsi:id_prov,nm_prov",
            "wilayah",
            "wilayah.profile",
            "cabang",
            "cabang.prov:id_prov,nm_prov",
            "cabang.profile",
            "corestatus",
        ])
            ->where('id', '=', $coretaxId)
            ->first();

        $coretaxJurney = Coretax::where('id', '!=', $coretaxId)
            ->where('id_user', '=', $coretax->id_user)
            ->where('status', '=', 'final aprove')
            ->orderByDesc('tgl_acc')
            ->orderByDesc('id')
            ->get();

        if (!$coretax) {
            return response()->json([
                'code' => 404,
                'message' => 'Coretax data not found',
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            "ctx" => $coretax,
            "ctxJurney" => $coretaxJurney,
        ], HttpResponse::HTTP_OK);
    }

    public function accepted(Coretax $coretax)
    {

        try {
            if ($coretax) {
                $coretax->update([
                    'status' => 'dokumen diproses',
                ]);
                CoretaxStatus::where([
                    'id_coretax' => $coretax->id,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Diterima Verifikator',
                    'status' => 'success',
                ]);
                CoretaxStatus::where([
                    'id_coretax' => $coretax->id,
                    'statusType' => 'dokumen diproses',
                ])->update([
                    'status' => 'success',
                    'keterangan' => null,
                ]);

                return redirect()->back()->with('success', 'Berhasil mengajukan permohonan Coretax');
            }
            return redirect()->back()->with('error', 'Invalid Coretax Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function openExpiry(Coretax $coretax)
    {

        try {
            if ($coretax) {
                $coretax->update([
                    'tgl_expiry' => Carbon::now(),
                ]);

                return redirect()->back()->with('success', 'Berhasil mengubah tanggal expiry');
            }
            return redirect()->back()->with('error', 'Invalid Coretax Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function rejected(Request $request, Coretax $coretax)
    {

        try {
            if ($coretax) {
                $coretax->update([
                    'status' => 'perbaikan',
                ]);
                CoretaxStatus::where([
                    'id_coretax' => $coretax->id,
                    'statusType' => 'perbaikan',
                ])->update([
                    'textstatus' => 'Ditolak Verifikator',
                    'status' => 'failed',
                    'keterangan' => $request->keterangan,
                ]);
                CoretaxStatus::where([
                    'id_coretax' => $coretax->id,
                    'statusType' => 'dokumen diproses',
                ])->update([
                    'status' => null,
                    'keterangan' => null
                ]);

                return redirect()->back()->with('success', 'Permohonan Coretax ditolak');
            }
            return redirect()->back()->with('error', 'Invalid Coretax Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function appeared(Request $request, Coretax $coretax)
    {

        try {
            $request->validate([
                'nitku' => 'nullable|string|max:100',
                'tgl_expiry' => 'required',
            ]);

            if ($coretax) {
                $coretax->update([
                    'tgl_acc' => Carbon::now(),
                    'tgl_expiry' => $request->tgl_expiry,
                    'nitku' => $request->nitku,
                    'status' => 'final aprove',
                ]);
                CoretaxStatus::where([
                    'id_coretax' => $coretax->id,
                    'statusType' => 'final aprove',
                ])->update([
                    'status' => 'success',
                    'keterangan' => $request->keterangan,
                ]);

                return redirect()->back()->with('success', 'Pengajuan layanan coretax berhasil di approve');
            }
            return redirect()->back()->with('error', 'Invalid Coretax Id');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', 'Kesalahan data pada form');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }

    public function destroy(Coretax $coretax)
    {
        try {
            if ($coretax) {

                $coretax->delete();
                return redirect()->back()->with('success', 'Berhasil menghapus pengajuan coretax');
            }
            return redirect()->back()->with('error', 'Invalid Coretax Id');
        } catch (\Exception $e) {
            throw new CatchErrorException($e);
        }
    }
}
