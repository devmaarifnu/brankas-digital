<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'npsn' => 'required|unique:satpen,npsn|size:8',
            'kabupaten' => 'required|numeric',
            'propinsi' => 'required|numeric',
            'cabang' => 'required|numeric',
            'jenjang' => 'required|numeric',
            'nm_satpen' => 'required|string',
            'yayasan' => 'required|string',
            'kepsek' => 'required|string',
            'telp' => 'required|string',
            'email' => 'required|email|string|unique:satpen,email',
            'thn_berdiri' => 'required|size:4',
            'kelurahan' => 'required|string',
            'alamat' => 'required|string',
            'aset_tanah' => 'required|in:jamiyah,masyarakat nu',
            'nm_pemilik' => 'required|string',
            'password' => 'required',
            'passconfirm' => 'required|same:password',
            'no_srt_permohonan' => 'required',
            'tgl_srt_permohonan' => 'required',
            'file_permohonan' => 'required|file|mimes:pdf|max:1024',
            'nm_rekom_pc' => 'required',
            'cabang_rekom_pc' => 'required',
            'no_srt_rekom_pc' => 'required',
            'tgl_srt_rekom_pc' => 'required',
            'file_rekom_pc' => 'required|file|mimes:pdf|max:1024',
            'nm_rekom_pw' => 'required',
            'wilayah_rekom_pw' => 'required',
            'no_srt_rekom_pw' => 'required',
            'tgl_srt_rekom_pw' => 'required',
            'file_rekom_pw' => 'required|file|mimes:pdf|max:1024',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'npsn.required' => 'NPSN wajib diisi.',
            'npsn.unique' => 'NPSN sudah terdaftar.',
            'npsn.size' => 'NPSN harus terdiri dari tepat 8 digit angka.',
            'kabupaten.required' => 'Kabupaten wajib dipilih.',
            'kabupaten.numeric' => 'Kabupaten tidak valid.',
            'propinsi.required' => 'Propinsi wajib dipilih.',
            'propinsi.numeric' => 'Propinsi tidak valid.',
            'cabang.required' => 'Cabang wajib dipilih.',
            'cabang.numeric' => 'Cabang tidak valid.',
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'jenjang.numeric' => 'Jenjang pendidikan tidak valid.',
            'nm_satpen.required' => 'Nama Satpen wajib diisi.',
            'nm_satpen.string' => 'Nama Satpen harus berupa teks.',
            'yayasan.required' => 'Yayasan wajib dipilih.',
            'yayasan.string' => 'Yayasan tidak valid.',
            'kepsek.required' => 'Nama kepala sekolah wajib diisi.',
            'kepsek.string' => 'Nama kepala sekolah harus berupa teks.',
            'telp.required' => 'Nomor HP/WA wajib diisi.',
            'telp.string' => 'Nomor HP/WA tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.string' => 'Email harus berupa teks.',
            'email.unique' => 'Email sudah terdaftar.',
            'thn_berdiri.required' => 'Tahun berdiri wajib diisi.',
            'thn_berdiri.size' => 'Tahun berdiri harus 4 digit.',
            'kelurahan.required' => 'Kelurahan wajib diisi.',
            'kelurahan.string' => 'Kelurahan harus berupa teks.',
            'alamat.required' => 'Alamat lengkap satpen wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'aset_tanah.required' => 'Aset tanah wajib dipilih.',
            'aset_tanah.in' => 'Aset tanah harus berupa salah satu pilihan yang tersedia.',
            'nm_pemilik.required' => 'Nama pemilik tanah wajib diisi.',
            'nm_pemilik.string' => 'Nama pemilik harus berupa teks.',
            'password.required' => 'Password akun wajib diisi.',
            'passconfirm.required' => 'Konfirmasi password wajib diisi.',
            'passconfirm.same' => 'Konfirmasi password tidak sama.',
            'no_srt_permohonan.required' => 'Nomor surat permohonan wajib diisi.',
            'tgl_srt_permohonan.required' => 'Tanggal surat permohonan wajib diisi.',
            'file_permohonan.required' => 'File permohonan wajib diunggah.',
            'file_permohonan.file' => 'File permohonan harus berupa berkas.',
            'file_permohonan.mimes' => 'File permohonan harus berformat PDF.',
            'file_permohonan.max' => 'Ukuran file permohonan maksimal 1 MB.',
            'nm_rekom_pc.required' => 'Pemberi keterangan cabang wajib diisi.',
            'cabang_rekom_pc.required' => 'Nama cabang wajib dipilih.',
            'no_srt_rekom_pc.required' => 'Nomor surat keterangan cabang wajib diisi.',
            'tgl_srt_rekom_pc.required' => 'Tanggal surat keterangan cabang wajib diisi.',
            'file_rekom_pc.required' => 'File keterangan PC wajib diunggah.',
            'file_rekom_pc.file' => 'File keterangan PC harus berupa berkas.',
            'file_rekom_pc.mimes' => 'File keterangan PC harus berformat PDF.',
            'file_rekom_pc.max' => 'Ukuran file keterangan PC maksimal 1 MB.',
            'nm_rekom_pw.required' => 'Pemberi rekomendasi wilayah wajib diisi.',
            'wilayah_rekom_pw.required' => 'Nama wilayah wajib dipilih.',
            'no_srt_rekom_pw.required' => 'Nomor surat rekomendasi wilayah wajib diisi.',
            'tgl_srt_rekom_pw.required' => 'Tanggal surat rekomendasi wilayah wajib diisi.',
            'file_rekom_pw.required' => 'File rekomendasi PW wajib diunggah.',
            'file_rekom_pw.file' => 'File rekomendasi PW harus berupa berkas.',
            'file_rekom_pw.mimes' => 'File rekomendasi PW harus berformat PDF.',
            'file_rekom_pw.max' => 'Ukuran file rekomendasi PW maksimal 1 MB.',
        ];
    }
}
