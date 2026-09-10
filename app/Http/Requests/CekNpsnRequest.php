<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CekNpsnRequest extends FormRequest
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
            'npsn' => 'required|size:8',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'npsn.required' => 'Silakan masukkan NPSN Anda.',
            'npsn.size' => 'NPSN harus terdiri dari tepat 8 digit angka.',
        ];
    }
}
