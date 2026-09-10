<?php

namespace App\Http\Controllers;

use App\Exceptions\CatchErrorException;
use App\Models\FileUpload;
use App\Models\Informasi;
use App\Models\Jenjang;
use App\Models\Provinsi;
use App\Models\Satpen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    public function homePage()
    {
        try {
            $jenjang = Jenjang::orderBy('id_jenjang')->get();
            $provinsi = Provinsi::orderBy('id_prov')->get();
            $jmlSatpenByKabupaten = DB::select("SELECT nama_kab, (SELECT COUNT(id_kab) FROM satpen WHERE id_kab=kabupaten.id_kab) AS jml_satpen FROM kabupaten");
            $jmlSatpenByJenjang = DB::select("SELECT id_jenjang, nm_jenjang, keterangan, (SELECT COUNT(id_jenjang) FROM satpen WHERE id_jenjang=jenjang_pendidikan.id_jenjang and status IN ('setujui','expired','perpanjangan')) AS jml_satpen FROM jenjang_pendidikan");
            $berandaInformasi = Informasi::orderBy('id_info')->limit(7)->get();
            $countSatpen = Satpen::whereIn('status', ['setujui', 'expired','perpanjangan'])->count('id_satpen');
            $topProvinsi = DB::select("SELECT nm_prov, (SELECT COUNT(id_prov) FROM satpen WHERE id_prov=provinsi.id_prov and status IN ('setujui','expired','perpanjangan')) AS record_count FROM provinsi ORDER BY record_count DESC LIMIT 6");
            return view('landing.home', compact('jmlSatpenByJenjang', 'jmlSatpenByKabupaten', 'berandaInformasi', 'countSatpen', 'provinsi', 'jenjang', 'topProvinsi'));

        } catch (\Exception $e) {
            throw new CatchErrorException("[HOME PAGE] has error ". $e);
        }
    }

    public function verifyDokumenPage($qrcode = null) {
        try {
            if ($qrcode) {
                $verifyData = FileUpload::where("qrcode", "=", request()->url())->first();
                if (!$verifyData) {
                    return view('landing.resultverify', compact('verifyData'));
                }
                $satpenData = Satpen::find($verifyData->id_satpen);
                return view('landing.resultverify', compact('verifyData', 'satpenData'));
            }
            return view('landing.verify');

        } catch (\Exception $e) {
            throw new CatchErrorException("[VERIFY DOCUMENT PAGE] has error ". $e);
        }
    }

    public function readInformasiPage($slug = null) {
            try {
                $berandaInformasi = Informasi::orderBy('id_info')->limit(5)->get();
                if ($slug) {
                    $readInfo = Informasi::with("file")->where('slug', '=', $slug)->first();
                    return view('landing.readinformasi', compact('berandaInformasi', 'readInfo'));
                }
                $listInformasi = Informasi::orderBy('id_info', 'DESC')->get();
                return view('landing.informasi', compact('listInformasi'));

            } catch (\Exception $e) {
                throw new CatchErrorException("[READ INFORMASI PAGE] has error ". $e);
            }
    }

    public function downloadFileInformasi(string $filename = null) {
        try {
            if (Storage::exists("fileInformasi/".$filename)){
                return response()->download(
                    storage_path("app/fileInformasi/" . $filename));
            }
            return response("File Not Found!");

        } catch (\Exception $e) {
            throw new CatchErrorException("[DOWNLOAD FILE INFORMASI] has error ". $e);
        }
    }

    public function contactPage() {
        return view('landing.kontak');
    }
}
