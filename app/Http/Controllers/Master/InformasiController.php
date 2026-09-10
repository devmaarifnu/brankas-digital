<?php

namespace App\Http\Controllers\Master;

use App\Exceptions\CatchErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditInformasiRequest;
use App\Http\Requests\PostInformasiRequest;
use App\Models\Informasi;
use App\Models\InformasiFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InformasiController extends Controller
{
    public function index() {

        $listInformasi = Informasi::get();
        return view('admin.informasi.kelolainformasi', compact('listInformasi'));
    }

    public function create() {

        return view('admin.informasi.tambah');
    }

    public function store(PostInformasiRequest $request) {
        try {
            $path = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public');
            }
            $post = Informasi::create([
                'slug' => Str::slug($request->headline),
                'headline' => $request->headline,
                'type' => $request->type,
                'content' => $request->contents,
                'tgl_upload' => Carbon::now(),
                'tag' => $request->tag,
                'image' => $path,
            ]);
            if ($request->hasFile('fileuploads')) {
                foreach ($request->file('fileuploads') as $file) {
                    $path =  $file->storeAs('fileInformasi', str_replace("#", "", $file->getClientOriginalName()));
                    InformasiFile::create([
                        'id_info' => $post->id_info,
                        'fileupload' => $path,
                    ]);
                }
            }

            return redirect()->route('informasi.index')->with('success', 'Berhasil posting informasi');

        } catch (\Exception $e) {
            throw new CatchErrorException("[INFORMASI STORE] has error ". $e);

        }
    }

    public function edit(Informasi $informasi) {
        return view('admin.informasi.edit', compact('informasi'));
    }

    public function update(EditInformasiRequest $request, Informasi $informasi) {
        try {
            $path = null;
            if ($request->hasFile('image')) {
                Storage::delete($informasi->image);
                $path = $request->file('image')->store('public');
            }
            $informasi->update([
                'slug' => Str::slug($request->headline),
                'headline' => $request->headline,
                'type' => $request->type,
                'content' => $request->contents,
                'tag' => $request->tag,
                'image' => $path ?? $informasi->image,
            ]);
            if ($request->hasFile('fileuploads')) {
                $files = InformasiFile::where('id_info', '=', $informasi->id_info);
                foreach ($files->get() as $file) {
                    Storage::delete($file->fileupload);
                }
                $files->delete();
                foreach ($request->file('fileuploads') as $file) {
                    $path =  $file->storeAs('fileInformasi', str_replace("#", "", $file->getClientOriginalName()));
                    InformasiFile::create([
                        'id_info' => $informasi->id_info,
                        'fileupload' => $path,
                    ]);
                }
            }

            return redirect()->route('informasi.index')->with('success', 'Berhasil update informasi');

        } catch (\Exception $e) {
            throw new CatchErrorException("[INFORMASI UPDATE] has error ". $e);

        }
    }

    public function destroy(Informasi $informasi) {
        try {
            $files = InformasiFile::where('id_info', '=', $informasi->id_info);
            foreach ($files->get() as $file) {
                Storage::delete($file->fileupload);
            }
            Storage::delete($informasi->image);
            $files->delete();
            $informasi->delete();

            return redirect()->route('informasi.index')->with('success', 'Berhasil menghapus informasi');

        } catch (\Exception $e) {
            throw new CatchErrorException("[INFORMASI DESTROY] has error ". $e);

        }
    }
}
