<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\{Kabupaten, Provinsi};
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class KabupatenController extends Controller
{
    public function index() {

        $listPropinsi = Provinsi::get();
        $listKabupaten = Kabupaten::with("prov")->get();
        return view('admin.master.datakabupaten', compact('listPropinsi', 'listKabupaten'));
    }

    public function store(Request $request) {
        try {
            Kabupaten::create([
                'id_prov' => $request->kode_prov,
                'nama_kab' => $request->nama_kab,
            ]);

            return redirect()->route('kabupaten.index')->with('success', 'Berhasil membuat kabupaten');

        } catch (\Exception $e) {
            throw new CatchErrorException("[KABUPATEN STORE] has error ". $e);

        }
    }

    public function show(Kabupaten $kabupaten) {
        try {
            return response()->json($kabupaten, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[KABUPATEN SHOW] has error ". $e);

        }
    }

    public function update(Request $request, Kabupaten $kabupaten) {
        try {
            $kabupaten->update([
                'id_prov' => $request->kode_prov,
                'nama_kab' => $request->nama_kab,
            ]);

            return redirect()->route('kabupaten.index')->with('success', 'Berhasil update kabupaten');

        } catch (\Exception $e) {
            throw new CatchErrorException("[KABUPATEN UPDATE] has error ". $e);

        }
    }

    public function destroy(Kabupaten $kabupaten) {
        try {
            $kabupaten->delete();

            return redirect()->route('kabupaten.index')->with('success', 'Berhasil menghapus kabupaten');

        } catch (\Exception $e) {
            throw new CatchErrorException("[KABUPATEN DESTROY] has error ". $e);

        }
    }
}
