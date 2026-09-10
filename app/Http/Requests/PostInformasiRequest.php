<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostInformasiRequest extends FormRequest
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
            'headline' => 'required',
            'type' => 'required',
            'image' => 'required',
            'contents' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'headline.required' => 'Judul (headline) informasi wajib diisi.',
            'type.required' => 'Tipe informasi wajib dipilih.',
            'image.required' => 'Gambar informasi wajib diunggah.',
            'contents.required' => 'Isi/konten informasi wajib diisi.',
        ];
    }
}
