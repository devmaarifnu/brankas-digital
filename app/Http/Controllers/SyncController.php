<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateQr;
use App\Helpers\ReferensiKemdikbud;
use App\Helpers\Strings;
use App\Http\Controllers\Admin\SATPENController as SatpenControllerAdmin;
use App\Http\Requests\StatusSatpenRequest;
use App\Http\Requests\SyncRequest;
use App\Models\FileUpload;
use App\Models\Jenjang;
use App\Models\Kabupaten;
use App\Models\PengurusCabang;
use App\Models\Provinsi;
use App\Models\Satpen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SyncController extends Controller
{
    public function bypassExistingData(SyncRequest $request) {

        /**
         * Cek npsn on system based on npsn number
         */
        if (Satpen::where(['npsn' => $request->npsn])->first()) {
            return response()->json(['message' => 'NPSN sudah terdaftar dalam sistem'],
                HttpResponse::HTTP_BAD_REQUEST);
        }

        $cloneSekolah = new ReferensiKemdikbud();
        $cloneSekolah->clone($request->npsn);

        if ($cloneSekolah->getStatus() && $cloneSekolah->getResult() !== null) {
            $jsonResultSekolah = $cloneSekolah->getResult();

            $keyProv = Strings::removeFirstWord($jsonResultSekolah["propinsiluar_negeri_ln"]);
            $keyKab = Strings::removeFirstWord($jsonResultSekolah["kabkotanegara_ln"]);
            $provinsi = Provinsi::where('nm_prov', 'like', $keyProv)->first();
            $kabupaten = Kabupaten::where('nama_kab', 'like', '%' . $keyKab . '%')->first();
            $cabang = PengurusCabang::where('nama_pc', 'like', '%' . $request->cabang . '%')->first();
            $jenjang = Jenjang::where('nm_jenjang', 'like', $request->jenjang)->first();

            $registerNumber = "";
            $lastOfSatpen = Satpen::orderBy('id_satpen', 'desc')->first();

            $orderedNumber = 0;
            if ($lastOfSatpen !== null) {
                $orderedNumber = (int)$lastOfSatpen->no_urut;
            }
            $orderedNumber = str_pad(++$orderedNumber, 4, '0', STR_PAD_LEFT);
            /**
             * When yayasan is not bhp nu append prefix A in generated number
             * Generated number is combined of Kode Provinsi + Kode Kabupaten + 4 digit ordered number
             */
            try {
                $registerNumber .= $provinsi->kode_prov . $cabang->kode_kab . $orderedNumber;

                return DB::transaction(function () use ($request, $registerNumber, $provinsi, $kabupaten, $cabang, $jenjang, $jsonResultSekolah, $orderedNumber) {

                    $piagamFilename = "Piagam Nomor Registrasi Ma'arif - ";
                    $skFilename = "SK Satuan Pendidikan BHPNU - ";
                    $currentDate = Date::now();

                    $user = AuthController::register($registerNumber, $registerNumber);

                    $satpen = Satpen::create([
                        'id_user' => $user->id_user,
                        'id_prov' => $provinsi->id_prov,
                        'id_kab' => $kabupaten->id_kab,
                        'id_pc' => $cabang->id_pc,
                        'id_jenjang' => $jenjang->id_jenjang,
                        'npsn' => $jsonResultSekolah["npsn"],
                        'no_registrasi' => $registerNumber,
                        'no_urut' => $orderedNumber,
                        'nm_satpen' => $jsonResultSekolah["nama"],
                        'yayasan' => strtolower($request->yayasan) <> "bhpnu" ? $request->yayasan : strtoupper($request->yayasan),
                        'kepsek' => $request->kepsek,
                        'telpon' => $request->telp,
                        'email' => $request->email,
                        'thn_berdiri' => $request->thn_berdiri,
                        'alamat' => $jsonResultSekolah["alamat"],
                        'kelurahan' => $jsonResultSekolah["desakelurahan"],
                        'kecamatan' => $jsonResultSekolah["kecamatankota_ln"],
                        'tgl_registrasi' => $currentDate,
                    ]);
                    if ($satpen) {

                        $piagamFilename .= $satpen->nm_satpen . ".pdf";
                        $skFilename .= $satpen->nm_satpen . ".pdf";

                        //create piagam
                        FileUpload::create([
                            'id_satpen' => $satpen->id_satpen,
                            'typefile' => "piagam",
                            'qrcode' => GenerateQr::encodeQr(),
                            'nm_file' => $piagamFilename,
                            'tgl_file' => $currentDate,
                        ]);
                        FileUpload::create([
                            'id_satpen' => $satpen->id_satpen,
                            'typefile' => "sk",
                            'qrcode' => GenerateQr::encodeQr(),
                            'nm_file' => $skFilename,
                            'tgl_file' => $currentDate,
                        ]);

                        (new SatpenControllerAdmin())->updateSatpenStatus((new StatusSatpenRequest())
                            ->merge([
                                "status_verifikasi" => "expired",
                                "keterangan" => "import by system",
                            ]),
                            $satpen);
                    }
                    return response()->json($satpen, HttpResponse::HTTP_OK);

                });
            } catch (\Exception $e) {
                return response()->json(["message" => $e->getMessage()], HttpResponse::HTTP_BAD_REQUEST);
            }
        }
        return response()->json(['message' => $cloneSekolah->getResult()],
                    HttpResponse::HTTP_BAD_REQUEST);
    }
}
