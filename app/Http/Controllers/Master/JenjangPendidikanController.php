<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class JenjangPendidikanController extends Controller
{
    public function index() {

        $listJenjang = Jenjang::get();
        return view('admin.master.datajenjang', compact('listJenjang'));
    }

    public function store(Request $request) {
        try {
            Jenjang::create([
                'nm_jenjang' => $request->nama_jenjang,
                'lembaga' => $request->lembaga,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('jenjang.index')->with('success', 'Berhasil membuat jenjang pendidikan');

        } catch (\Exception $e) {
            throw new CatchErrorException("[JENJANG STORE] has error ". $e);

        }
    }

    public function show(Jenjang $jenjang) {
        try {
            return response()->json($jenjang, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[JENJANG SHOW] has error ". $e);

        }
    }

    public function update(Request $request, Jenjang $jenjang) {
        try {
            $jenjang->update([
                'nm_jenjang' => $request->nama_jenjang,
                'lembaga' => $request->lembaga,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('jenjang.index')->with('success', 'Berhasil update jenjang pendidikan');

        } catch (\Exception $e) {
            throw new CatchErrorException("[JENJANG UPDATE] has error ". $e);

        }
    }

    public function destroy(Jenjang $jenjang) {
        try {
            $jenjang->delete();

            return redirect()->route('jenjang.index')->with('success', 'Berhasil menghapus jenjang pendidikan');

        } catch (\Exception $e) {
            throw new CatchErrorException("[JENJANG DESTROY] has error ". $e);

        }
    }
}
