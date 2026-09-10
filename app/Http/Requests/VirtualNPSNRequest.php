<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VirtualNPSNRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'nama_sekolah' => 'required',
            'jenjang' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'alamat' => 'required',
            'email' => 'required|unique:virtual_npsn,email|email',
            'nik_kepsek' => 'required|unique:virtual_npsn,nik_kepsek|min:16|max:16',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'provinsi.required' => 'Provinsi wajib dipilih.',
            'kabupaten.required' => 'Kabupaten/Kota wajib dipilih.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nik_kepsek.required' => 'NIK kepala sekolah wajib diisi.',
            'nik_kepsek.min' => 'NIK kepala sekolah harus terdiri dari tepat 16 digit angka.',
            'nik_kepsek.max' => 'NIK kepala sekolah harus terdiri dari tepat 16 digit angka.',
            'nik_kepsek.unique' => 'NIK sudah digunakan.',
        ];
    }
}
