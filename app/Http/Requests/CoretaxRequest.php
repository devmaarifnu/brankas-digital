<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoretaxRequest extends FormRequest
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
        $isEditing = $this->isMethod('put') || $this->isMethod('patch') || $this->has('id');

        return [
            'nitku' => 'nullable|string|max:255',
            'nm_pic' => 'required|string|max:100',
            'nik_pic' => 'required|string|max:20',
            'whatsapp_pic' => 'required|string|max:20',
            'npwp_lama' => ($isEditing ? 'nullable' : 'required').'|file|mimes:pdf|max:512',
        ];
    }

     /**
      * @return string[]
      */
     public function messages()
     {
         return [
             'nitku.string' => 'NITKU harus berupa teks.',
             'nitku.max' => 'NITKU maksimal 255 karakter.',
             'nm_pic.required' => 'Nama PIC wajib diisi.',
             'nm_pic.string' => 'Nama PIC harus berupa teks.',
             'nm_pic.max' => 'Nama PIC maksimal 100 karakter.',
             'nik_pic.required' => 'NIK PIC wajib diisi.',
             'nik_pic.string' => 'NIK PIC harus berupa teks.',
             'nik_pic.max' => 'NIK PIC maksimal 20 karakter.',
             'whatsapp_pic.required' => 'Nomor WhatsApp PIC wajib diisi.',
             'whatsapp_pic.string' => 'Nomor WhatsApp PIC harus berupa teks.',
             'whatsapp_pic.max' => 'Nomor WhatsApp PIC maksimal 20 karakter.',
             'npwp_lama.required' => 'File NPWP lama wajib diunggah.',
             'npwp_lama.file' => 'NPWP lama harus berupa berkas.',
             'npwp_lama.mimes' => 'File NPWP lama harus berformat PDF.',
             'npwp_lama.max' => 'Ukuran file NPWP lama maksimal 512 KB.',
         ];
     }
}
