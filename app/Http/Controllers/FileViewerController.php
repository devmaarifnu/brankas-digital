<?php

namespace App\Http\Controllers;


class FileViewerController extends Controller
{
    public function viewOSSDoc(string $path, string $fileName) {
        $fileName = $path.'/'.$fileName;
        if ($fileName) {
            $filepath = storage_path("app/oss-doc/". $fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }

    public function viewBuktiPembayaran(string $fileName) {
        if ($fileName) {
            $filepath = storage_path("app/bhpnu-doc/bukti-bayar/". $fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }

    public function viewNpwpLama(string $fileName) {
        if ($fileName) {
            $filepath = storage_path("app/coretax-doc/npwp-lama/". $fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }

    public function viewSkPtk(string $path, string $fileName) {
        $fileName = $path.'/'.$fileName;
        if ($fileName) {
            $filepath = storage_path("app/ptk-doc/". $fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }

    public function pdfUploadViewer(string $fileName) {
        if ($fileName) {
            $filepath = storage_path("app/uploads/".$fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }

    public function pdfGeneratedViewer(string $type=null, string $fileName=null) {
        if ($fileName && $type) {
            $filepath = storage_path("app/generated/".$type."/".$fileName);

            if (!file_exists($filepath)) return response("File not found!");
            return response()->file($filepath);
        }
        return response("Invalid Document!");
    }
}
