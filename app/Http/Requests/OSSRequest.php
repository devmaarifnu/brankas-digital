<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OSSRequest extends FormRequest
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
            'email' => 'required|email',
            'npwp' => 'required|string',
            'no_whatsapp' => 'required|string',
            'bukti_bayar' => 'nullable|file|mimes:pdf,jpg,jpeg,png,svg|max:1024',
            'intansi_izin_lama' => 'nullable|string',
            'nomor_izin_lama' => 'nullable|string',
            'tgl_terbit_izin_lama' => 'nullable|date',
            'tgl_expired_izin_lama' => 'nullable|date',
            'file_izin_lama' => 'nullable|file|mimes:pdf|max:1524',
            'lokasi_usaha' => 'required|string',
            'luas_lahan_usaha' => 'required|numeric',
            'apakah_sudah_menempati_lahan' => 'required|in:Sudah,Belum',
            'status_lahan' => 'required|in:Milik Sendiri,Sewa,Pinjam Pakai',
            'ms_instansi_izin' => 'nullable|string',
            'ms_nomor_izin' => 'nullable|string',
            'ms_tgl_terbit' => 'nullable|date',
            'ms_tgl_expired' => 'nullable|date',
            'ms_file_lampiran' => 'nullable|file|mimes:pdf|max:4024',
            'sw_pemilik_lahan' => 'nullable|string',
            'sw_nomor_perjanjian' => 'nullable|string',
            'sw_tgl_perjanjian' => 'nullable|date',
            'sw_tgl_expired' => 'nullable|date',
            'sw_file_lampiran' => 'nullable|file|mimes:pdf|max:1024',
            'pp_pemilik_lahan' => 'nullable|string',
            'pp_nomor_perjanjian' => 'nullable|string',
            'pp_tgl_perjanjian' => 'nullable|date',
            'pp_tgl_expired' => 'nullable|date',
            'pp_file_lampiran' => 'nullable|file|mimes:pdf|max:3024',
            'apakah_memerlukan_bangunan_baru' => 'required|in:Iya,Tidak',
            'sudah_ada_bangunan' => 'required|in:Sudah,Belum',
            'status_bangunan_usaha' => 'required|in:Milik Sendiri,Sewa,Pinjam Pakai',
            'jumlah_bangunan' => 'required|integer',
            'apakah_memiliki_imb' => 'nullable|in:Iya,Tidak',
            'imb_jml_bangunan' => 'nullable|integer',
            'imb_pejabat_penerbit_izin' => 'nullable|string',
            'imb_nomor' => 'nullable|string',
            'imb_tgl_terbit' => 'nullable|date',
            'imb_tgl_expired' => 'nullable|date',
            'imb_file_lampiran' => 'nullable|file|mimes:pdf|max:1024',
            'apakah_memiliki_sertifikat_slf' => 'nullable|in:Iya,Tidak',
            'slf_pejabat_penerbit' => 'nullable|string',
            'slf_nomor' => 'nullable|string',
            'slf_tgl_terbit' => 'nullable|date',
            'slf_tgl_expired' => 'nullable|date',
            'slf_file_lampiran' => 'nullable|file|mimes:pdf|max:1024',
            'apakah_lokasi_sekolah_lintas_perbatasan' => 'required|in:Iya,Tidak',
            'alamat_sekolah' => 'required|string',
            'propinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kode_pos' => 'required|string',
            'file_peta_polygon' => 'nullable|file|mimes:zip|max:1024',
            'apakah_proyek_strategi_nasional' => 'required|in:Iya,Tidak',
            'rencana_teknis_bangunan' => 'nullable|file|mimes:pdf|max:2024',
            'kawasan_lokasi_usaha' => 'required|in:Diluar Kawasan,Didalam Kawasan',
            'klu_nama_kawasan_industri' => 'nullable|string',
            'apakah_memiliki_kkpr' => 'nullable|in:Iya,Tidak',
            'pejabat_penerbit_kkpr' => 'nullable|string',
            'nomor_kkpr' => 'nullable|string',
            'tgl_terbit_kkpr' => 'nullable|date',
            'tgl_expired_kkpr' => 'nullable|date',
            'file_lampiran_kkpr' => 'nullable|file|mimes:pdf|max:1024',
            'dri_pembelian_tanah' => 'required|numeric',
            'dri_bangunan' => 'required|numeric',
            'dri_mesin_dalam_negeri' => 'required|numeric',
            'dri_mesin_impor' => 'required|numeric',
            'dri_investasi' => 'required|numeric',
            'dri_modal_kerja_3_bulan' => 'required|numeric',
            'tgl_mulai_beroperasi' => 'required|string',
            'jml_pegawai_pria' => 'required|integer',
            'jml_pegawai_wanita' => 'required|integer',
            'jml_pegawai_asing' => 'required|integer',
            'apakah_memiliki_izin_amdal' => 'nullable|in:Iya,Tidak',
            'amdal_pejabat_penerbit' => 'nullable|string',
            'amdal_nomor_izin' => 'nullable|string',
            'amdal_tgl_terbit' => 'nullable|date',
            'amdal_tgl_expired' => 'nullable|date',
            'amdal_file_lampiran' => 'nullable|file|mimes:pdf|max:1024',
            'apakah_memiliki_uklupl' => 'nullable|in:Iya,Tidak',
            'uklupl_pejabat_penerbit' => 'nullable|string',
            'uklupl_nomor_izin' => 'nullable|string',
            'uklupl_tgl_terbit' => 'nullable|date',
            'uklupl_tgl_expired' => 'nullable|date',
            'uklupl_file_lampiran' => 'nullable|file|mimes:pdf|max:1024',
        ];
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'npwp.required' => 'NPWP Sekolah wajib diisi.',
            'npwp.string' => 'NPWP harus berupa teks.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.string' => 'Nomor WhatsApp harus berupa teks.',
            'bukti_bayar.file' => 'Bukti pembayaran harus berupa berkas.',
            'bukti_bayar.mimes' => 'Format file bukti pembayaran harus PDF, JPG, JPEG, PNG, atau SVG.',
            'bukti_bayar.max' => 'Ukuran file bukti pembayaran maksimal 1 MB.',

            'intansi_izin_lama.string' => 'Nama instansi penerbit izin harus berupa teks.',
            'nomor_izin_lama.string' => 'Nomor izin lama harus berupa teks.',
            'tgl_terbit_izin_lama.date' => 'Tanggal terbit izin lama harus berupa tanggal yang valid.',
            'tgl_expired_izin_lama.date' => 'Tanggal expired izin lama harus berupa tanggal yang valid.',
            'file_izin_lama.file' => 'File izin lama harus berupa berkas.',
            'file_izin_lama.mimes' => 'File izin lama harus berformat PDF.',
            'file_izin_lama.max' => 'Ukuran file izin lama maksimal 1,5 MB.',

            'lokasi_usaha.required' => 'Lokasi kegiatan usaha wajib diisi.',
            'lokasi_usaha.string' => 'Lokasi usaha harus berupa teks.',
            'luas_lahan_usaha.required' => 'Luas lahan usaha wajib diisi.',
            'luas_lahan_usaha.numeric' => 'Luas lahan usaha harus berupa angka.',
            'apakah_sudah_menempati_lahan.required' => 'Pilih apakah sudah menempati lahan tersebut.',
            'apakah_sudah_menempati_lahan.in' => 'Pilihan status lahan tidak valid.',
            'status_lahan.required' => 'Status lahan wajib dipilih.',
            'status_lahan.in' => 'Status lahan harus berupa salah satu pilihan yang tersedia.',

            'ms_instansi_izin.string' => 'Nama instansi penerbit izin harus berupa teks.',
            'ms_nomor_izin.string' => 'Nomor izin harus berupa teks.',
            'ms_tgl_terbit.date' => 'Tanggal terbit harus berupa tanggal yang valid.',
            'ms_tgl_expired.date' => 'Tanggal habis masa berlaku harus berupa tanggal yang valid.',
            'ms_file_lampiran.file' => 'Lampiran harus berupa berkas.',
            'ms_file_lampiran.mimes' => 'Lampiran harus berformat PDF.',
            'ms_file_lampiran.max' => 'Ukuran lampiran maksimal 4 MB.',

            'sw_pemilik_lahan.string' => 'Nama pemilik lahan harus berupa teks.',
            'sw_nomor_perjanjian.string' => 'Nomor perjanjian harus berupa teks.',
            'sw_tgl_perjanjian.date' => 'Tanggal perjanjian harus berupa tanggal yang valid.',
            'sw_tgl_expired.date' => 'Tanggal habis masa berlaku harus berupa tanggal yang valid.',
            'sw_file_lampiran.file' => 'Lampiran perjanjian sewa harus berupa berkas.',
            'sw_file_lampiran.mimes' => 'Lampiran perjanjian sewa harus berformat PDF.',
            'sw_file_lampiran.max' => 'Ukuran lampiran perjanjian sewa maksimal 1 MB.',

            'pp_pemilik_lahan.string' => 'Nama pemilik lahan harus berupa teks.',
            'pp_nomor_perjanjian.string' => 'Nomor perjanjian harus berupa teks.',
            'pp_tgl_perjanjian.date' => 'Tanggal perjanjian harus berupa tanggal yang valid.',
            'pp_tgl_expired.date' => 'Tanggal habis masa berlaku harus berupa tanggal yang valid.',
            'pp_file_lampiran.file' => 'Lampiran perjanjian pinjam pakai harus berupa berkas.',
            'pp_file_lampiran.mimes' => 'Lampiran perjanjian pinjam pakai harus berformat PDF.',
            'pp_file_lampiran.max' => 'Ukuran lampiran perjanjian pinjam pakai maksimal 3 MB.',

            'apakah_memerlukan_bangunan_baru.required' => 'Pilih apakah memerlukan bangunan baru untuk kegiatan usaha.',
            'apakah_memerlukan_bangunan_baru.in' => 'Pilihan tidak valid.',
            'sudah_ada_bangunan.required' => 'Pilih apakah sudah ada bangunan.',
            'sudah_ada_bangunan.in' => 'Pilihan tidak valid.',
            'status_bangunan_usaha.required' => 'Status bangunan usaha wajib dipilih.',
            'status_bangunan_usaha.in' => 'Status bangunan usaha harus berupa salah satu pilihan yang tersedia.',
            'jumlah_bangunan.required' => 'Jumlah bangunan wajib diisi.',
            'jumlah_bangunan.integer' => 'Jumlah bangunan harus berupa angka bulat.',

            'apakah_memiliki_imb.in' => 'Pilihan IMB tidak valid.',
            'imb_jml_bangunan.integer' => 'Jumlah bangunan ber-IMB harus berupa angka bulat.',
            'imb_pejabat_penerbit_izin.string' => 'Nama pejabat penerbit IMB harus berupa teks.',
            'imb_nomor.string' => 'Nomor IMB harus berupa teks.',
            'imb_tgl_terbit.date' => 'Tanggal terbit IMB harus berupa tanggal yang valid.',
            'imb_tgl_expired.date' => 'Tanggal expired IMB harus berupa tanggal yang valid.',
            'imb_file_lampiran.file' => 'Lampiran IMB harus berupa berkas.',
            'imb_file_lampiran.mimes' => 'Lampiran IMB harus berformat PDF.',
            'imb_file_lampiran.max' => 'Ukuran lampiran IMB maksimal 1 MB.',

            'apakah_memiliki_sertifikat_slf.in' => 'Pilihan sertifikat SLF tidak valid.',
            'slf_pejabat_penerbit.string' => 'Nama pejabat penerbit SLF harus berupa teks.',
            'slf_nomor.string' => 'Nomor sertifikat SLF harus berupa teks.',
            'slf_tgl_terbit.date' => 'Tanggal terbit SLF harus berupa tanggal yang valid.',
            'slf_tgl_expired.date' => 'Tanggal expired SLF harus berupa tanggal yang valid.',
            'slf_file_lampiran.file' => 'Lampiran SLF harus berupa berkas.',
            'slf_file_lampiran.mimes' => 'Lampiran SLF harus berformat PDF.',
            'slf_file_lampiran.max' => 'Ukuran lampiran SLF maksimal 1 MB.',

            'apakah_lokasi_sekolah_lintas_perbatasan.required' => 'Pilih apakah lokasi sekolah berada dalam lintas perbatasan.',
            'apakah_lokasi_sekolah_lintas_perbatasan.in' => 'Pilihan tidak valid.',
            'alamat_sekolah.required' => 'Alamat sekolah wajib diisi.',
            'alamat_sekolah.string' => 'Alamat sekolah harus berupa teks.',
            'propinsi.required' => 'Propinsi wajib dipilih.',
            'propinsi.string' => 'Propinsi harus berupa teks.',
            'kabupaten.required' => 'Kabupaten/Kota wajib dipilih.',
            'kabupaten.string' => 'Kabupaten/Kota harus berupa teks.',
            'kecamatan.required' => 'Kecamatan wajib dipilih.',
            'kecamatan.string' => 'Kecamatan harus berupa teks.',
            'kelurahan.required' => 'Desa/Kelurahan wajib diisi.',
            'kelurahan.string' => 'Desa/Kelurahan harus berupa teks.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'kode_pos.string' => 'Kode pos harus berupa teks.',
            'file_peta_polygon.file' => 'Peta polygon harus berupa berkas.',
            'file_peta_polygon.mimes' => 'File peta polygon harus berformat ZIP.',
            'file_peta_polygon.max' => 'Ukuran file peta polygon maksimal 1 MB.',

            'apakah_proyek_strategi_nasional.required' => 'Pilih apakah merupakan proyek strategis nasional.',
            'apakah_proyek_strategi_nasional.in' => 'Pilihan tidak valid.',
            'rencana_teknis_bangunan.file' => 'Rencana teknis bangunan harus berupa berkas.',
            'rencana_teknis_bangunan.mimes' => 'Rencana teknis bangunan harus berformat PDF.',
            'rencana_teknis_bangunan.max' => 'Ukuran rencana teknis bangunan maksimal 2 MB.',
            'kawasan_lokasi_usaha.required' => 'Kawasan lokasi usaha wajib dipilih.',
            'kawasan_lokasi_usaha.in' => 'Kawasan lokasi usaha harus berupa salah satu pilihan yang tersedia.',
            'klu_nama_kawasan_industri.string' => 'Nama kawasan industri harus berupa teks.',

            'apakah_memiliki_kkpr.in' => 'Pilihan KKPR tidak valid.',
            'pejabat_penerbit_kkpr.string' => 'Nama pejabat penerbit KKPR harus berupa teks.',
            'nomor_kkpr.string' => 'Nomor izin KKPR harus berupa teks.',
            'tgl_terbit_kkpr.date' => 'Tanggal terbit KKPR harus berupa tanggal yang valid.',
            'tgl_expired_kkpr.date' => 'Tanggal expired KKPR harus berupa tanggal yang valid.',
            'file_lampiran_kkpr.file' => 'Lampiran KKPR harus berupa berkas.',
            'file_lampiran_kkpr.mimes' => 'Lampiran KKPR harus berformat PDF.',
            'file_lampiran_kkpr.max' => 'Ukuran lampiran KKPR maksimal 1 MB.',

            'dri_pembelian_tanah.required' => 'Data rencana investasi pembelian tanah wajib diisi.',
            'dri_pembelian_tanah.numeric' => 'Pembelian tanah harus berupa angka (Rupiah).',
            'dri_bangunan.required' => 'Data rencana investasi bangunan/gedung wajib diisi.',
            'dri_bangunan.numeric' => 'Bangunan/gedung harus berupa angka (Rupiah).',
            'dri_mesin_dalam_negeri.required' => 'Data rencana investasi mesin dalam negeri wajib diisi.',
            'dri_mesin_dalam_negeri.numeric' => 'Mesin dalam negeri harus berupa angka (Rupiah).',
            'dri_mesin_impor.required' => 'Data rencana investasi mesin impor wajib diisi.',
            'dri_mesin_impor.numeric' => 'Mesin impor harus berupa angka (Rupiah).',
            'dri_investasi.required' => 'Data rencana investasi lain-lain wajib diisi.',
            'dri_investasi.numeric' => 'Investasi lain-lain harus berupa angka (Rupiah).',
            'dri_modal_kerja_3_bulan.required' => 'Data modal kerja 3 bulanan wajib diisi.',
            'dri_modal_kerja_3_bulan.numeric' => 'Modal kerja harus berupa angka (Rupiah).',

            'tgl_mulai_beroperasi.required' => 'Tanggal mulai beroperasi wajib diisi.',
            'tgl_mulai_beroperasi.string' => 'Tanggal mulai beroperasi tidak valid.',
            'jml_pegawai_pria.required' => 'Jumlah tenaga kerja laki-laki wajib diisi.',
            'jml_pegawai_pria.integer' => 'Jumlah tenaga kerja laki-laki harus berupa angka bulat.',
            'jml_pegawai_wanita.required' => 'Jumlah tenaga kerja perempuan wajib diisi.',
            'jml_pegawai_wanita.integer' => 'Jumlah tenaga kerja perempuan harus berupa angka bulat.',
            'jml_pegawai_asing.required' => 'Jumlah tenaga kerja asing wajib diisi.',
            'jml_pegawai_asing.integer' => 'Jumlah tenaga kerja asing harus berupa angka bulat.',

            'apakah_memiliki_izin_amdal.in' => 'Pilihan izin AMDAL tidak valid.',
            'amdal_pejabat_penerbit.string' => 'Nama pejabat penerbit AMDAL harus berupa teks.',
            'amdal_nomor_izin.string' => 'Nomor izin AMDAL harus berupa teks.',
            'amdal_tgl_terbit.date' => 'Tanggal terbit AMDAL harus berupa tanggal yang valid.',
            'amdal_tgl_expired.date' => 'Tanggal expired AMDAL harus berupa tanggal yang valid.',
            'amdal_file_lampiran.file' => 'Lampiran AMDAL harus berupa berkas.',
            'amdal_file_lampiran.mimes' => 'Lampiran AMDAL harus berformat PDF.',
            'amdal_file_lampiran.max' => 'Ukuran lampiran AMDAL maksimal 1 MB.',

            'apakah_memiliki_uklupl.in' => 'Pilihan izin UKL-UPL tidak valid.',
            'uklupl_pejabat_penerbit.string' => 'Nama pejabat penerbit UKL-UPL harus berupa teks.',
            'uklupl_nomor_izin.string' => 'Nomor izin UKL-UPL harus berupa teks.',
            'uklupl_tgl_terbit.date' => 'Tanggal terbit UKL-UPL harus berupa tanggal yang valid.',
            'uklupl_tgl_expired.date' => 'Tanggal expired UKL-UPL harus berupa tanggal yang valid.',
            'uklupl_file_lampiran.file' => 'Lampiran UKL-UPL harus berupa berkas.',
            'uklupl_file_lampiran.mimes' => 'Lampiran UKL-UPL harus berformat PDF.',
            'uklupl_file_lampiran.max' => 'Ukuran lampiran UKL-UPL maksimal 1 MB.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Jika file di-upload, simpan ke storage sementara
            if ($this->hasFile('bukti_bayar')) {
                $file = $this->file('bukti_bayar');
                $path = $file->storeAs('uploads/temp', $file->getClientOriginalName());
                session(['temp_file_path' => $path]);
            }
        });
    }
}
