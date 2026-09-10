<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use App\Models\InformasiFile;
use App\Models\Jenjang;
use App\Models\Kabupaten;
use App\Models\Kategori;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\TahunPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiMasterController extends Controller
{
    // =========================================================================
    // PROVINSI
    // =========================================================================

    public function listProvinsi()
    {
        $provinsi = Provinsi::orderBy('nm_prov', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $provinsi
        ], HttpResponse::HTTP_OK);
    }

    public function showProvinsi($id)
    {
        $provinsi = Provinsi::with(['kabupaten', 'cabang'])->find($id);
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }
        return response()->json(['success' => true, 'data' => $provinsi], HttpResponse::HTTP_OK);
    }

    public function storeProvinsi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_prov' => 'required|string|max:10',
            'nm_prov' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $provinsi = Provinsi::create([
            'kode_prov' => $request->kode_prov,
            'nm_prov' => $request->nm_prov,
        ]);

        return response()->json(['success' => true, 'message' => 'Provinsi berhasil ditambahkan.', 'data' => $provinsi], HttpResponse::HTTP_CREATED);
    }

    public function updateProvinsi(Request $request, $id)
    {
        $provinsi = Provinsi::find($id);
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        $provinsi->update($request->only(['kode_prov', 'nm_prov']));

        return response()->json(['success' => true, 'message' => 'Provinsi berhasil diupdate.', 'data' => $provinsi], HttpResponse::HTTP_OK);
    }

    public function destroyProvinsi($id)
    {
        $provinsi = Provinsi::find($id);
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        $provinsi->delete();
        return response()->json(['success' => true, 'message' => 'Provinsi berhasil dihapus.'], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // KABUPATEN
    // =========================================================================

    public function listKabupaten(Request $request)
    {
        $query = Kabupaten::query();
        if ($request->filled('id_prov')) {
            $query->where('id_prov', $request->id_prov);
        }
        $kabupaten = $query->orderBy('nama_kab', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $kabupaten
        ], HttpResponse::HTTP_OK);
    }

    public function showKabupaten($id)
    {
        $kabupaten = Kabupaten::with('provinsi')->find($id);
        if (!$kabupaten) {
            return response()->json(['success' => false, 'message' => 'Kabupaten tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }
        return response()->json(['success' => true, 'data' => $kabupaten], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // PENGURUS CABANG (PC)
    // =========================================================================

    public function listCabang(Request $request)
    {
        $query = PengurusCabang::query();
        if ($request->filled('id_prov')) {
            $query->where('id_prov', $request->id_prov);
        }
        $cabang = $query->orderBy('nama_pc', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $cabang
        ], HttpResponse::HTTP_OK);
    }

    public function showCabang($id)
    {
        $cabang = PengurusCabang::with(['prov', 'profile'])->find($id);
        if (!$cabang) {
            return response()->json(['success' => false, 'message' => 'Pengurus Cabang tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }
        return response()->json(['success' => true, 'data' => $cabang], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // JENJANG PENDIDIKAN
    // =========================================================================

    public function listJenjang()
    {
        $jenjang = Jenjang::orderBy('id_jenjang', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $jenjang
        ], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // KATEGORI SATPEN
    // =========================================================================

    public function listKategori()
    {
        $kategori = Kategori::all();
        return response()->json([
            'success' => true,
            'data' => $kategori
        ], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // TAHUN PELAJARAN (TAPEL)
    // =========================================================================

    public function listTapel()
    {
        $tapel = TahunPelajaran::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $tapel
        ], HttpResponse::HTTP_OK);
    }

    public function storeTapel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tapel_dapo' => 'required|string',
            'nama_tapel' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tapel = TahunPelajaran::create([
            'tapel_dapo' => $request->tapel_dapo,
            'nama_tapel' => $request->nama_tapel,
        ]);

        return response()->json(['success' => true, 'message' => 'Tahun Pelajaran berhasil ditambahkan.', 'data' => $tapel], HttpResponse::HTTP_CREATED);
    }

    public function updateTapel(Request $request, $id)
    {
        $tapel = TahunPelajaran::find($id);
        if (!$tapel) {
            return response()->json(['success' => false, 'message' => 'Tahun Pelajaran tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        $tapel->update($request->only(['tapel_dapo', 'nama_tapel']));
        return response()->json(['success' => true, 'message' => 'Tahun Pelajaran berhasil diupdate.', 'data' => $tapel], HttpResponse::HTTP_OK);
    }

    public function destroyTapel($id)
    {
        $tapel = TahunPelajaran::find($id);
        if (!$tapel) {
            return response()->json(['success' => false, 'message' => 'Tahun Pelajaran tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        $tapel->delete();
        return response()->json(['success' => true, 'message' => 'Tahun Pelajaran berhasil dihapus.'], HttpResponse::HTTP_OK);
    }

    // =========================================================================
    // INFORMASI & PENGUMUMAN
    // =========================================================================

    public function listInformasi(Request $request)
    {
        $query = Informasi::with('file');
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $informasi = $query->orderBy('tgl_upload', 'desc')->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $informasi
        ], HttpResponse::HTTP_OK);
    }

    public function showInformasi($slugOrId)
    {
        $informasi = Informasi::with('file')
            ->where('id_info', $slugOrId)
            ->orWhere('slug', $slugOrId)
            ->first();

        if (!$informasi) {
            return response()->json(['success' => false, 'message' => 'Informasi tidak ditemukan.'], HttpResponse::HTTP_NOT_FOUND);
        }

        return response()->json(['success' => true, 'data' => $informasi], HttpResponse::HTTP_OK);
    }
}
