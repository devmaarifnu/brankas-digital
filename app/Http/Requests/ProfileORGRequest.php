<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileORGRequest extends FormRequest
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
            'id_pw' => 'required|numeric',
            'id_pc' => 'numeric',
            'alamat' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'lintang' => 'nullable|string|max:50',
            'bujur' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:50',
            'ketua' => 'required|string|max:100',
            'wakil_ketua' => 'required|string|max:100',
            'bendahara' => 'required|string|max:100',
            'sekretaris' => 'required|string|max:100',
            'telp_ketua' => 'required|digits_between:10,15',
            'telp_wakil' => 'required|digits_between:10,15',
            'telp_sekretaris' => 'required|digits_between:10,15',
            'telp_bendahara' => 'required|digits_between:10,15',
            'masa_khidmat' => 'required|string|max:9',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'id_pw.required' => 'Kode wilayah wajib diisi.',
            'id_pw.numeric' => 'Kode wilayah harus berupa angka.',
            'id_pc.numeric' => 'Kode cabang harus berupa angka.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'kelurahan.required' => 'Kelurahan wajib diisi.',
            'kelurahan.string' => 'Kelurahan harus berupa teks.',
            'kelurahan.max' => 'Kelurahan maksimal 100 karakter.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'kecamatan.string' => 'Kecamatan harus berupa teks.',
            'kecamatan.max' => 'Kecamatan maksimal 100 karakter.',
            'kabupaten.required' => 'Kabupaten wajib diisi.',
            'kabupaten.string' => 'Kabupaten harus berupa teks.',
            'kabupaten.max' => 'Kabupaten maksimal 100 karakter.',
            'lintang.string' => 'Latitude harus berupa teks.',
            'lintang.max' => 'Latitude maksimal 50 karakter.',
            'bujur.string' => 'Longitude harus berupa teks.',
            'bujur.max' => 'Longitude maksimal 50 karakter.',
            'website.string' => 'Website harus berupa teks.',
            'website.max' => 'Website maksimal 50 karakter.',
            'ketua.required' => 'Nama ketua wajib diisi.',
            'ketua.string' => 'Nama ketua harus berupa teks.',
            'ketua.max' => 'Nama ketua maksimal 100 karakter.',
            'wakil_ketua.required' => 'Wakil ketua wajib diisi.',
            'wakil_ketua.string' => 'Wakil ketua harus berupa teks.',
            'wakil_ketua.max' => 'Wakil ketua maksimal 100 karakter.',
            'bendahara.required' => 'Bendahara wajib diisi.',
            'bendahara.string' => 'Bendahara harus berupa teks.',
            'bendahara.max' => 'Bendahara maksimal 100 karakter.',
            'sekretaris.required' => 'Sekretaris wajib diisi.',
            'sekretaris.string' => 'Sekretaris harus berupa teks.',
            'sekretaris.max' => 'Sekretaris maksimal 100 karakter.',
            'telp_ketua.required' => 'Nomor telepon ketua wajib diisi.',
            'telp_ketua.digits_between' => 'Nomor telepon ketua harus terdiri dari 10 sampai 15 digit angka.',
            'telp_wakil.required' => 'Nomor telepon wakil wajib diisi.',
            'telp_wakil.digits_between' => 'Nomor telepon wakil harus terdiri dari 10 sampai 15 digit angka.',
            'telp_sekretaris.required' => 'Nomor telepon sekretaris wajib diisi.',
            'telp_sekretaris.digits_between' => 'Nomor telepon sekretaris harus terdiri dari 10 sampai 15 digit angka.',
            'telp_bendahara.required' => 'Nomor telepon bendahara wajib diisi.',
            'telp_bendahara.digits_between' => 'Nomor telepon bendahara harus terdiri dari 10 sampai 15 digit angka.',
            'masa_khidmat.required' => 'Masa khidmat wajib diisi.',
            'masa_khidmat.string' => 'Masa khidmat harus berupa teks.',
            'masa_khidmat.max' => 'Masa khidmat maksimal 9 karakter.',
        ];
    }
}
