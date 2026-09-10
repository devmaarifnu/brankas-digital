<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PDPTKRequest extends FormRequest
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
            'satpenId' => 'required|integer',
            'tapel' => 'required|string|max:9',
            'pd_lk' => 'required|integer|min:0',
            'pd_pr' => 'required|integer|min:0',
            'jml_pd' => 'required|integer|min:0',
            'guru_lk' => 'required|integer|min:0',
            'guru_pr' => 'required|integer|min:0',
            'jml_guru' => 'required|integer|min:0',
            'tendik_lk' => 'required|integer|min:0',
            'tendik_pr' => 'required|integer|min:0',
            'jml_tendik' => 'required|integer|min:0',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'satpenId.required' => 'ID Satuan Pendidikan wajib diisi.',
            'satpenId.integer' => 'ID Satuan Pendidikan harus berupa angka.',
            'satpenId.exists' => 'ID Satuan Pendidikan tidak ditemukan di database.',

            'tapel.required' => 'Tahun Pelajaran wajib diisi.',
            'tapel.string' => 'Tahun Pelajaran harus berupa teks.',
            'tapel.max' => 'Tahun Pelajaran maksimal 9 karakter.',

            'pd_lk.required' => 'Jumlah peserta didik laki-laki wajib diisi.',
            'pd_lk.integer' => 'Jumlah peserta didik laki-laki harus berupa angka bulat.',
            'pd_lk.min' => 'Jumlah peserta didik laki-laki tidak boleh kurang dari 0.',

            'pd_pr.required' => 'Jumlah peserta didik perempuan wajib diisi.',
            'pd_pr.integer' => 'Jumlah peserta didik perempuan harus berupa angka bulat.',
            'pd_pr.min' => 'Jumlah peserta didik perempuan tidak boleh kurang dari 0.',

            'jml_pd.required' => 'Jumlah total peserta didik wajib diisi.',
            'jml_pd.integer' => 'Jumlah total peserta didik harus berupa angka bulat.',
            'jml_pd.min' => 'Jumlah total peserta didik tidak boleh kurang dari 0.',

            'guru_lk.required' => 'Jumlah guru laki-laki wajib diisi.',
            'guru_lk.integer' => 'Jumlah guru laki-laki harus berupa angka bulat.',
            'guru_lk.min' => 'Jumlah guru laki-laki tidak boleh kurang dari 0.',

            'guru_pr.required' => 'Jumlah guru perempuan wajib diisi.',
            'guru_pr.integer' => 'Jumlah guru perempuan harus berupa angka bulat.',
            'guru_pr.min' => 'Jumlah guru perempuan tidak boleh kurang dari 0.',

            'jml_guru.required' => 'Jumlah total guru wajib diisi.',
            'jml_guru.integer' => 'Jumlah total guru harus berupa angka bulat.',
            'jml_guru.min' => 'Jumlah total guru tidak boleh kurang dari 0.',

            'tendik_lk.required' => 'Jumlah tendik laki-laki wajib diisi.',
            'tendik_lk.integer' => 'Jumlah tendik laki-laki harus berupa angka bulat.',
            'tendik_lk.min' => 'Jumlah tendik laki-laki tidak boleh kurang dari 0.',

            'tendik_pr.required' => 'Jumlah tendik perempuan wajib diisi.',
            'tendik_pr.integer' => 'Jumlah tendik perempuan harus berupa angka bulat.',
            'tendik_pr.min' => 'Jumlah tendik perempuan tidak boleh kurang dari 0.',

            'jml_tendik.required' => 'Jumlah total tendik wajib diisi.',
            'jml_tendik.integer' => 'Jumlah total tendik harus berupa angka bulat.',
            'jml_tendik.min' => 'Jumlah total tendik tidak boleh kurang dari 0.',
        ];
    }
}
