<?php

namespace App\Http\Requests;

use App\Exceptions\MyValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SyncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return void
     *
     * @throws MyValidationException
     */
    protected function failedValidation(Validator $validator): MyValidationException
    {
        throw new MyValidationException($validator);
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
            'thn_berdiri' => 'nullable|size:4',
            'yayasan' => 'required',
            'jenjang' => 'required',
            'telp' => 'nullable|max:13',
            'email' => 'nullable|email',
            'cabang' => 'required',
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
            'thn_berdiri.size' => 'Tahun berdiri harus 4 digit.',
            'yayasan.required' => 'Yayasan wajib diisi.',
            'jenjang.required' => 'Jenjang pendidikan wajib diisi.',
            'telp.max' => 'Nomor telepon maksimal 13 karakter.',
            'email.email' => 'Format email tidak valid.',
            'cabang.required' => 'Cabang wajib diisi.',
        ];
    }
}
