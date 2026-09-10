<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\CatchErrorException;
use App\Helpers\ReferensiKemdikbud;
use App\Models\Kabupaten;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiController extends Controller
{
    public function getSatpenById($satpenId=null) {
        try {
            if ($satpenId) {
                $satpenProfile = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'cabang', 'jenjang', 'filereg', 'timeline' => function($query){
                        $query->skip($query->count("*") - 7)->limit(7);
                    }])
                    ->where('id_satpen', '=', $satpenId)
                    ->first();
                if (!$satpenProfile) return response()->json(['error' => 'Forbidden to access satpen profile']);

                return response()->json($satpenProfile, HttpResponse::HTTP_OK);

            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET SATPEN BY ID] has error ". $e);

        }
    }

    public function searchSatpen(Request $request) {
        try {
            if ($request) {
                $filter = [];
                $keywordFilter = [];
                if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
                if ($request->kab) $filter["id_kab"] = $request->kab;
                if ($request->prov) $filter["id_prov"] = $request->prov;
                if ($request->kecamatan){
                    array_push($keywordFilter, ["kecamatan", "like", "%". $request->kecamatan ."%"]);
                }
                $listSatpen = Satpen::with([
                    'kategori:id_kategori,nm_kategori',
                    'provinsi:id_prov,nm_prov',
                    'kabupaten:id_kab,nama_kab',
                    'jenjang:id_jenjang,nm_jenjang'])
                    ->select('id_kategori','id_kab', 'id_prov', 'id_jenjang', 'npsn', 'nm_satpen', 'kecamatan', 'kelurahan', 'alamat')
                    ->where('status', '=', 'setujui')
                    ->where($filter)
                    ->where(function ($query) use ($keywordFilter) {
                            foreach ($keywordFilter as $condition) {
                                $query->where(...$condition);
                            }
                        })
                    ->get();
                if (!$listSatpen) return response()->json(['error' => 'Forbidden to access satpen profile']);

                return response()->json($listSatpen, HttpResponse::HTTP_OK);

            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET SATPEN BY ID] has error ". $e);

        }
    }

    public function getKabupatenByProv($provId=null) {
        try {
            if ($provId) {
                $provs = Kabupaten::where('id_prov', '=', $provId)->get();
                if (!$provs) return response()->json(['error' => 'Forbidden to access kabupaten']);

                return response()->json($provs, HttpResponse::HTTP_OK);

            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET KABUPATEN BY PROV] has error ". $e);

        }
    }

    public function getPCByProv($provId=null) {
        try {
            if ($provId) {
                $provs = PengurusCabang::where('id_prov', '=', $provId);
                if (in_array(auth()->user()->role, ["admin cabang"])) {
                    $pcId = auth()->user()->cabangId;
                    $provs = $provs->where('id_pc', '=', $pcId);
                }
                $provs = $provs->get();
                if (!$provs) return response()->json(['error' => 'Forbidden to access kabupaten']);

                return response()->json($provs, HttpResponse::HTTP_OK);

            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PC BY PROV] has error ". $e);

        }
    }

    public function getProvAndCount() {
        try {
            $additionQuery = " ";
            if (in_array(@auth()->user()->role, ["admin wilayah", "admin cabang"])) {
                $provId = auth()->user()->provId;
                $additionQuery .= "WHERE id_prov='$provId'";
            }
            $recordPerPropinsi = DB::select("SELECT nm_prov, map,
                                                        (SELECT COUNT(id_prov) FROM satpen WHERE id_prov=provinsi.id_prov and status IN ('setujui','expired','perpanjangan')) AS record_count
                                                         FROM provinsi $additionQuery");
            if (!$recordPerPropinsi) return response()->json(['error' => 'Forbidden to access record']);

            return response()->json($recordPerPropinsi, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PROV AND COUNT] has error ". $e);

        }

    }

    public function getKabAndCount($provId=null) {
        try {
            if (!$provId) $provId = Provinsi::min("id_prov");
            $recordPerKabupaten = DB::select("SELECT nama_kab, (SELECT COUNT(id_kab) FROM satpen WHERE id_kab=kabupaten.id_kab and status IN ('setujui','expired','perpanjangan')) AS record_count FROM kabupaten WHERE id_prov='$provId'");

            if (!$recordPerKabupaten) return response()->json(['error' => 'Forbidden to access record']);

            return response()->json($recordPerKabupaten, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[GET KAB AND COUNT] has error ". $e);

        }
    }

    public function getPCAndCount() {
        try {
            $additionQuery = " ";
            if (in_array(auth()->user()->role, ["admin cabang"])) {
                $pcId = auth()->user()->cabangId;
                $additionQuery .= "WHERE id_pc='$pcId'";
            } elseif (in_array(auth()->user()->role, ["admin wilayah"])) {
                $provId = auth()->user()->provId;
                $additionQuery .= "WHERE id_prov='$provId'";
            }

            $recordPerKabupaten = DB::select("SELECT nama_pc, (SELECT COUNT(id_pc) FROM satpen WHERE id_pc=pengurus_cabang.id_pc and status IN ('setujui','expired','perpanjangan')) AS record_count FROM pengurus_cabang $additionQuery");

            if (!$recordPerKabupaten) return response()->json(['error' => 'Forbidden to access record']);

            return response()->json($recordPerKabupaten, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[GET PC AND COUNT] has error ". $e);

        }
    }

    public function getJenjangAndCount($provId = null) {
        try {
            $additionQuery = " ";
            
            // Check for user role restrictions first
            if (in_array(auth()->user()->role, ["admin wilayah"])) {
                $userProvId = auth()->user()->provId;
                $additionQuery .= "AND id_prov='$userProvId'";
            }
            elseif (in_array(auth()->user()->role, ["admin cabang"])) {
                $pcId = auth()->user()->cabangId;
                $additionQuery .= "AND id_pc='$pcId'";
            }
            // If a specific province ID is provided and user has permission, filter by it
            elseif ($provId && in_array(auth()->user()->role, ["super admin", "admin pusat"])) {
                $additionQuery .= "AND id_prov='$provId'";
            }
            
            $recordByJenjang = DB::select("SELECT nm_jenjang, keterangan, (SELECT COUNT(id_jenjang) FROM satpen WHERE id_jenjang=jenjang_pendidikan.id_jenjang and status IN ('setujui','expired','perpanjangan') $additionQuery ) AS record_count FROM jenjang_pendidikan");
            
            if (!$recordByJenjang) return response()->json(['error' => 'Forbidden to access record']);

            return response()->json($recordByJenjang, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[GET JENJANG AND COUNT] has error ". $e);

        }
    }

    public function getJenjangAndCountByCabang($provId = null, $cabangId = null) {
        try {
            $additionQuery = " ";
            
            // Check for user role restrictions first
            if (in_array(auth()->user()->role, ["admin wilayah"])) {
                $userProvId = auth()->user()->provId;
                $additionQuery .= "AND id_prov='$userProvId'";
            }
            elseif (in_array(auth()->user()->role, ["admin cabang"])) {
                $pcId = auth()->user()->cabangId;
                $additionQuery .= "AND id_pc='$pcId'";
            }

            if ($cabangId) {
                $additionQuery .= "AND id_pc='$cabangId'";
            } elseif ($provId) {
                $additionQuery .= "AND id_prov='$provId'";
            }
            
            $recordByJenjang = DB::select("SELECT nm_jenjang, keterangan, (SELECT COUNT(id_jenjang) FROM satpen WHERE id_jenjang=jenjang_pendidikan.id_jenjang and status IN ('setujui','expired','perpanjangan') $additionQuery ) AS record_count FROM jenjang_pendidikan");
            
            if (!$recordByJenjang) return response()->json(['error' => 'Forbidden to access record']);

            return response()->json($recordByJenjang, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[GET JENJANG AND COUNT BY CABANG] has error ". $e);

        }
    }

    public function checkNPSNtoReferensiData($npsn=null) {
        try {
            if ($npsn) {
                $cloneSekolah = new ReferensiKemdikbud();
                $cloneSekolah->clone($npsn);

                if ($cloneSekolah->getStatus() && $cloneSekolah->getResult() !== null) {
                    $jsonResultSekolah = $cloneSekolah->getResult();
                    return response()->json($jsonResultSekolah, HttpResponse::HTTP_OK);
                }
                return response()->json(['error' => $cloneSekolah->getResult()]);
            }
        } catch (\Exception $e) {
            throw new CatchErrorException("[GET SATPEN BY ID] has error ". $e);

        }
    }

    /**
     * Get kabupaten by provinsi for dropdown filter
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKabupatenByProvinsi(Request $request)
    {
        try {
            $provinsi_id = $request->query('provinsi_id');
            
            if (!$provinsi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provinsi ID is required',
                    'data' => []
                ], 400);
            }

            $kabupaten = Kabupaten::where('id_prov', $provinsi_id)
                ->orderBy('nama_kab')
                ->select('id_kab as id', 'nama_kab as nama')
                ->get();

            return response()->json($kabupaten, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kabupaten: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
