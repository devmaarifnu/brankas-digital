<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\CatchErrorException;
use App\Helpers\MailService;
use App\Helpers\ReferensiKemdikbud;
use App\Helpers\DapoMaarifNU;
use App\Models\Jenjang;
use App\Models\Kabupaten;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use App\Models\User;
use App\Models\VirtualNPSN;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiAuthController extends Controller
{
    /**
     * API Login - returns Bearer Token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.'
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        if ($user->status_active === 'block') {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini telah dinonaktifkan (blocked).'
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        // Load relations
        $user->load(['wilayah', 'cabang']);

        // Check if operator, also attach satpen info
        $satpen = null;
        if ($user->role === 'operator') {
            $satpen = Satpen::where('id_user', $user->id_user)->first();
        }

        // Generate Sanctum token
        $token = $user->createToken('sipinter-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'satpen' => $satpen,
            ]
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Current authenticated user profile
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['wilayah', 'cabang']);

        $satpen = null;
        if ($user->role === 'operator') {
            $satpen = Satpen::with(['kategori', 'provinsi', 'kabupaten', 'cabang', 'jenjang', 'filereg', 'timeline'])
                ->where('id_user', $user->id_user)
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'satpen' => $satpen,
            ]
        ], HttpResponse::HTTP_OK);
    }

    /**
     * API Logout - revoke current token
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dihapus.'
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Check NPSN via Kemdikbud / Dapo Ma'arif / Virtual NPSN
     */
    public function checkNpsn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // 1. Cek Virtual NPSN di database
            $virtualNpsn = VirtualNPSN::with([
                "jenjang:id_jenjang,nm_jenjang",
                "provinsi:id_prov,nm_prov",
                "kabupaten:id_kab,nama_kab"
            ])
            ->where("nomor_virtual", "=", $request->npsn)
            ->first();

            if ($virtualNpsn) {
                if (!Carbon::now()->gt(Carbon::parse($virtualNpsn->expired_after))) {
                    return response()->json([
                        'success' => true,
                        'source' => 'virtual_npsn',
                        'data' => [
                            "nama" => $virtualNpsn->nama_sekolah,
                            "npsn" => $virtualNpsn->nomor_virtual,
                            "alamat" => $virtualNpsn->alamat ?? "",
                            "desakelurahan" => "",
                            "kecamatankota_ln" => "",
                            "kabkotanegara_ln" => $virtualNpsn->kabupaten?->nama_kab ?? "",
                            "propinsiluar_negeri_ln" => "PROV. " . ($virtualNpsn->provinsi?->nm_prov ?? ""),
                            "bentuk_pendidikan" => $virtualNpsn->jenjang?->nm_jenjang ?? "",
                        ]
                    ], HttpResponse::HTTP_OK);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor NPSN Virtual sudah kadaluarsa (expired).'
                ], HttpResponse::HTTP_BAD_REQUEST);
            }

            // 2. Cek apakah sudah terdaftar di sistem
            if (Satpen::where('npsn', $request->npsn)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'NPSN sudah pernah terdaftar dalam sistem SIPINTER.'
                ], HttpResponse::HTTP_CONFLICT);
            }

            // 3. Cek Dapo Maarif NU
            $cloneSekolah = new DapoMaarifNU();
            $cloneSekolah->clone($request->npsn);

            if (!$cloneSekolah->getStatus()) {
                // 4. Cek referensi.data.kemdikbud.go.id
                $cloneSekolah = new ReferensiKemdikbud();
                $cloneSekolah->clone($request->npsn);
            }

            if ($cloneSekolah->getStatus() && $cloneSekolah->getResult() !== null) {
                $result = $cloneSekolah->getResult();
                return response()->json([
                    'success' => true,
                    'source' => 'kemdikbud/dapo',
                    'data' => $result
                ], HttpResponse::HTTP_OK);
            }

            return response()->json([
                'success' => false,
                'message' => $cloneSekolah->getResult() ?? 'Data sekolah dengan NPSN tersebut tidak ditemukan.'
            ], HttpResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa NPSN: ' . $e->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Request Virtual NPSN
     */
    public function requestVirtualNPSN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenjang' => 'required|integer',
            'provinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'alamat' => 'required|string',
            'nama_sekolah' => 'required|string',
            'email' => 'required|email',
            'nik_kepsek' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $vnpsn = VirtualNPSN::create([
                'id_jenjang' => $request->jenjang,
                'id_prov' => $request->provinsi,
                'id_kab' => $request->kabupaten,
                'alamat' => $request->alamat,
                'nama_sekolah' => $request->nama_sekolah,
                'email' => $request->email,
                'nik_kepsek' => $request->nik_kepsek,
            ]);

            // Kirim email notifikasi
            MailService::send([
                "to" => $request->email,
                "subject" => "Permohonan Virtual NPSN",
                "recipient" => $request->nama_sekolah,
                "content" => "<p>Anda telah melakukan permohonan NPSN Virtual untuk Sekolah {$request->nama_sekolah}.</p>
                            <p>Permohonan NPSN telah dikirimkan. Data sedang divalidasi oleh Admin, silahkan tunggu balasan NPSN Virtual oleh admin pada email ini.</p>
                            <p>Setelah mendapatkan NPSN Virtual, VNPSN akan terhapus otomatis setelah 2 minggu jika tidak digunakan.</p>
                            <h4>Jika dalam 2 hari kerja belum memperoleh balasan, silahkan kontak Admin.</h4>"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permohonan VNPSN berhasil dikirimkan. Tunggu NPSN Virtual dikirimkan oleh admin melalui email.',
                'data' => $vnpsn
            ], HttpResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permohonan Virtual NPSN: ' . $e->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_pass' => 'required|string',
            'new_pass' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($request->user()->id_user);

        if (!Hash::check($request->last_pass, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.'
            ], HttpResponse::HTTP_BAD_REQUEST);
        }

        $user->update([
            'password' => Hash::make($request->new_pass)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ], HttpResponse::HTTP_OK);
    }
}
