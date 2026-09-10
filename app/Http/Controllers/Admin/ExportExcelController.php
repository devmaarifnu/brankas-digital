<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Cabang;
use App\Exports\PDPTK;
use App\Exports\Others;
use App\Exports\SatpenExport;
use App\Exports\Wilayah;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcelController extends Controller
{

    public function exportSatpentoExcel(Request $request)
    {
        $statuses = ['setujui', 'expired', 'perpanjangan'];
        $filter = [];
        if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
        if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
        if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
        if ($request->kategori) $filter["id_kategori"] = $request->kategori;
        if ($request->status) $statuses = [$request->status];
        if ($request->keyword) array_push($filter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);

        return Excel::download(new SatpenExport(request()->specificFilter, $statuses, $filter), 'exported_data.xlsx');
    }

    public function exportPDPTKtoExcel(Request $request)
    {
        $filter = [];
        $keywordFilter = [];
        $lembaga = ["SEKOLAH", "MADRASAH"];
        $filled = ["0", "1"];
        if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
        if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
        if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
        if ($request->kategori) $filter["id_kategori"] = $request->kategori;
        if ($request->lembaga) $lembaga = [strtoupper($request->lembaga)];
        if ($request->filled) $filled = [$request->filled == "true" ? "1" : "0"];
        if ($request->keyword) array_push($filter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
        if ($request->keyword) {
            array_push($keywordFilter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["npsn", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["no_registrasi", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["yayasan", "like", "%" . $request->keyword . "%"]);
        }

        return Excel::download(new PDPTK(request()->specificFilter, $lembaga, $request->tapel ?? Settings::get('current_tapel'), $filter, $keywordFilter, $filled), 'exported_pdptk_data.xlsx');
    }

    public function exportOthersDatatoExcel(Request $request)
    {
        $filter = [];
        $keywordFilter = [];
        $lembaga = ["SEKOLAH", "MADRASAH"];
        $lingkungan_satpen = ["Sekolah berbasis Pondok Pesantren", "Sekolah Boarding", "Sekolah biasa", ""];
        $akreditasi = ["A", "B", "C", "-"];
        if ($request->jenjang) $filter["id_jenjang"] = $request->jenjang;
        if ($request->kabupaten) $filter["id_kab"] = $request->kabupaten;
        if ($request->provinsi) $filter["id_prov"] = $request->provinsi;
        if ($request->kategori) $filter["id_kategori"] = $request->kategori;
        if ($request->lembaga) $lembaga = [strtoupper($request->lembaga)];
        if ($request->akreditasi) $akreditasi = [strtoupper($request->akreditasi)];
        if ($request->lingkungan_satpen) $lingkungan_satpen = [$request->lingkungan_satpen];
        if ($request->keyword) array_push($filter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
        if ($request->keyword) {
            array_push($keywordFilter, ["nm_satpen", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["npsn", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["no_registrasi", "like", "%" . $request->keyword . "%"]);
            array_push($keywordFilter, ["yayasan", "like", "%" . $request->keyword . "%"]);
        }

        return Excel::download(new Others(request()->specificFilter, $lembaga, $filter, $keywordFilter, $lingkungan_satpen, $akreditasi), 'exported_others_data.xlsx');
    }

    public function exportWilayahtoExcel()
    {
        return Excel::download(new Wilayah(), 'exported_wilayah_data.xlsx');
    }

    public function exportCabangtoExcel()
    {
        $wilayah = request()->wilayah;
        return Excel::download(new Cabang(request()->specificFilter, $wilayah), 'exported_cabang_data.xlsx');
    }
}
