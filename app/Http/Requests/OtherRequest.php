<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtherRequest extends FormRequest
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
            'satpenId' => 'required',
            'npyp' => 'required',
            'naungan' => 'required',
            'no_sk_pendirian' => 'required',
            'tgl_sk_pendirian' => 'required',
            'no_sk_operasional' => 'required',
            'tgl_sk_operasional' => 'required',
            'akreditasi' => 'required',
            'website' => 'required',
            'lingkungan_satpen' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'satpenId.required' => 'ID Satuan Pendidikan wajib diisi',
            'npyp.required' => 'NPYP wajib diisi',
            'naungan.required' => 'Naungan wajib diisi',
            'no_sk_pendirian.required' => 'Nomor SK Pendirian wajib diisi',
            'tgl_sk_pendirian.required' => 'Tanggal SK Pendirian wajib diisi',
            'no_sk_operasional.required' => 'Nomor SK Operasional wajib diisi',
            'tgl_sk_operasional.required' => 'Tanggal SK Operasional wajib diisi',
            'akreditasi.required' => 'Akreditasi wajib diisi',
            'website.required' => 'Website wajib diisi',
            'lingkungan_satpen.required' => 'Lingkungan Satpen wajib diisi',
        ];
    }
}
