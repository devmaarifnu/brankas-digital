<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BHPNURequest extends FormRequest
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
            'bukti_bayar' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'bukti_bayar.required' => 'Bukti pembayaran wajib diunggah.',
        ];
    }
}
