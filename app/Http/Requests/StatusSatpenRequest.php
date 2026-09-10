<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusSatpenRequest extends FormRequest
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
            'status_verifikasi' => 'required|string|in:revisi,proses dokumen,terima,expired,perpanjangan',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.string' => 'Status verifikasi tidak valid.',
            'status_verifikasi.in' => 'Status verifikasi harus berupa salah satu opsi yang tersedia.',
        ];
    }
}
