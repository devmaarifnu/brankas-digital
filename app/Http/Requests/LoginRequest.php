<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'username.required' => 'Silakan masukkan username Anda.',
            'password.required' => 'Silakan masukkan password Anda.',
        ];
    }
}
