<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'last_pass' => 'required',
            'new_pass' => 'required',
            'confirm_pass' => 'required|same:new_pass',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'last_pass.required' => 'Password lama wajib diisi.',
            'new_pass.required' => 'Password baru wajib diisi.',
            'confirm_pass.required' => 'Konfirmasi password wajib diisi.',
            'confirm_pass.same' => 'Konfirmasi password tidak sama.',
        ];
    }
}
