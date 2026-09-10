<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    /**
     * Display a listing of short URLs.
     */
    public function index(Request $request)
    {
        $query = ShortUrl::query()->with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%")
                  ->orWhere('original_url', 'like', "%{$search}%");
            });
        }

        $shortUrls = $query->paginate(15)->withQueryString();
        $totalUrls = ShortUrl::count();
        $totalClicks = ShortUrl::sum('clicks');

        return view('admin.shorturl.index', compact('shortUrls', 'totalUrls', 'totalClicks'));
    }

    /**
     * Store a newly created short URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|string|max:2048',
            'title'        => 'nullable|string|max:255',
            'custom_alias' => 'nullable|string|min:3|max:50|regex:/^[a-zA-Z0-9\-_]+$/|unique:short_urls,short_code',
        ], [
            'original_url.required' => 'URL tujuan wajib diisi.',
            'custom_alias.min'      => 'Custom link minimal 3 karakter.',
            'custom_alias.max'      => 'Custom link maksimal 50 karakter.',
            'custom_alias.regex'    => 'Custom link hanya boleh berisi huruf, angka, tanda strip (-), dan underscore (_).',
            'custom_alias.unique'   => 'Custom link ini sudah digunakan, silakan pilih link lain.',
        ]);

        $originalUrl = trim($request->input('original_url'));
        if (!preg_match('~^https?://~i', $originalUrl)) {
            $originalUrl = 'http://' . $originalUrl;
        }

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return redirect()->back()->withInput()->with('error', 'Format URL tujuan tidak valid.');
        }

        $reservedWords = ['admin', 'api', 'login', 'logout', 'register', 'satpen', 'npyp', 'oss', 'bhpnu', 'coretax', 'kontak', 'informasi', 'verify', 'ceknpsn', 'npsnvirtual', 'master', 'storage', 'css', 'js', 'images'];

        if ($request->filled('custom_alias')) {
            $shortCode = trim($request->input('custom_alias'));
            if (in_array(strtolower($shortCode), $reservedWords)) {
                return redirect()->back()->withInput()->with('error', 'Nama link tersebut termasuk kata kunci sistem yang dicadangkan.');
            }
        } else {
            $shortCode = $this->generateUniqueCode();
        }

        ShortUrl::create([
            'title'        => $request->input('title'),
            'short_code'   => $shortCode,
            'original_url' => $originalUrl,
            'clicks'       => 0,
            'id_user'      => auth()->user() ? auth()->user()->id_user : null,
        ]);

        return redirect()->route('shorturl.index')->with('success', 'Tautan pendek berhasil dibuat: ' . url('/s/' . $shortCode));
    }

    /**
     * Update the specified short URL.
     */
    public function update(Request $request, $id)
    {
        $shortUrl = ShortUrl::findOrFail($id);

        $request->validate([
            'original_url' => 'required|string|max:2048',
            'title'        => 'nullable|string|max:255',
            'short_code'   => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9\-_]+$/|unique:short_urls,short_code,' . $id . ',id_shorturl',
        ], [
            'original_url.required' => 'URL tujuan wajib diisi.',
            'short_code.required'   => 'Kode link wajib diisi.',
            'short_code.min'        => 'Kode link minimal 3 karakter.',
            'short_code.max'        => 'Kode link maksimal 50 karakter.',
            'short_code.regex'      => 'Kode link hanya boleh berisi huruf, angka, strip, dan underscore.',
            'short_code.unique'     => 'Kode link ini sudah digunakan oleh tautan lain.',
        ]);

        $originalUrl = trim($request->input('original_url'));
        if (!preg_match('~^https?://~i', $originalUrl)) {
            $originalUrl = 'http://' . $originalUrl;
        }

        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return redirect()->back()->withInput()->with('error', 'Format URL tujuan tidak valid.');
        }

        $shortUrl->update([
            'title'        => $request->input('title'),
            'short_code'   => trim($request->input('short_code')),
            'original_url' => $originalUrl,
        ]);

        return redirect()->route('shorturl.index')->with('success', 'Data tautan pendek berhasil diperbarui.');
    }

    /**
     * Remove the specified short URL.
     */
    public function destroy($id)
    {
        $shortUrl = ShortUrl::findOrFail($id);
        $shortUrl->delete();

        return redirect()->route('shorturl.index')->with('success', 'Tautan pendek berhasil dihapus.');
    }

    /**
     * Redirect visitor from short URL to destination URL.
     */
    public function redirectShort($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            abort(404, 'Tautan pendek tidak ditemukan atau telah dihapus.');
        }

        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }

    /**
     * Helper to generate unique 6-character random alphanumeric string.
     */
    private function generateUniqueCode($length = 6)
    {
        do {
            $code = Str::random($length);
        } while (ShortUrl::where('short_code', $code)->exists());

        return $code;
    }
}
