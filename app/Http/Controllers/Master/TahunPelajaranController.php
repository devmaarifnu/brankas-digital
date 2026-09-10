<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TahunPelajaranController extends Controller
{
    public function index() {

        $listTapel = TahunPelajaran::orderBy('id', 'desc')->get();
        return view('admin.master.data-tapel', compact('listTapel'));
    }

    public function store(Request $request) {
        try {
            TahunPelajaran::create([
                'tapel_dapo' => $request->tapel_dapo,
                'nama_tapel' => $request->nama_tapel,
            ]);

            return redirect()->route('tapel.index')->with('success', 'Berhasil menambahkan tahun pelajaran');

        } catch (\Exception $e) {
            throw new CatchErrorException("[TAHUN PELAJARAN STORE] has error ". $e);
        }
    }

    public function show(TahunPelajaran $tapel) {
        try {
            return response()->json($tapel, HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            throw new CatchErrorException("[TAHUN PELAJARAN SHOW] has error ". $e);

        }
    }

    public function update(Request $request, TahunPelajaran $tapel) {
        try {
            $tapel->update([
                'tapel_dapo' => $request->tapel_dapo,
                'nama_tapel' => $request->nama_tapel,
            ]);

            return redirect()->route('tapel.index')->with('success', 'Berhasil update tahun pelajaran');

        } catch (\Exception $e) {
            throw new CatchErrorException("[TAHUN PELAJARAN UPDATE] has error ". $e);

        }
    }

    public function destroy(TahunPelajaran $tapel) {
        try {
            $tapel->delete();

            return redirect()->route('tapel.index')->with('success', 'Berhasil menghapus tahun pelajaran');

        } catch (\Exception $e) {
            throw new CatchErrorException("[TAHUN PELAJARAN DESTROY] has error ". $e);

        }
    }
}
