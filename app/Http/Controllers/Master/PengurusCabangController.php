<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\{PengurusCabang, Provinsi};
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PengurusCabangController extends Controller
{
    public function index() {

        $listPropinsi = Provinsi::get();
        $listCabang = PengurusCabang::with("prov")->get();
        return view('admin.master.datacabang', compact('listPropinsi', 'listCabang'));
    }

    public function store(Request $request) {
        try {
            PengurusCabang::create([
                'id_prov' => $request->kode_prov,
                'kode_kab' => $request->kode_kab,
                'nama_pc' => $request->nama_pc,
            ]);

            return redirect()->route('cabang.index')->with('success', 'Berhasil membuat cabang');

        } catch (\Exception $e) {
            throw new CatchErrorException("[CABANG STORE] has error ". $e);

        }
    }

    public function show(PengurusCabang $cabang) {
        try {
            return response()->json($cabang, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[CABANG SHOW] has error ". $e);

        }
    }

    public function update(Request $request, PengurusCabang $cabang) {
        try {
            $cabang->update([
                'id_prov' => $request->kode_prov,
                'kode_kab' => $request->kode_kab,
                'nama_pc' => $request->nama_pc,
            ]);

            return redirect()->route('cabang.index')->with('success', 'Berhasil update pengurus cabang');

        } catch (\Exception $e) {
            throw new CatchErrorException("[CABANG UPDATE] has error ". $e);

        }
    }

    public function destroy(PengurusCabang $cabang) {
        try {
            $cabang->delete();

            return redirect()->route('cabang.index')->with('success', 'Berhasil menghapus cabang');

        } catch (\Exception $e) {
            throw new CatchErrorException("[CABANG DESTROY] has error ". $e);

        }
    }
}
