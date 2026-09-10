@extends('template.general', [
    'title' => 'Sipinter - Register'
])

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-selectpicker.css') }}" />
@endsection

@section('container')
<div class="d-flex flex-column align-items-center">
    <div class="container-fluid login-side-right">
        <div class="row justify-content-sm-center align-items-center" style="height:25vh;">
            <div class="col-sm-10">
                <a href="{{ route('home') }}" class="text-nowrap logo-img d-block py-2 w-100">
                    <img src="{{ asset('assets/images/logos/Logo_Sipinter_Panjang.png') }}" width="210" alt="">
                    <h6 class="fw-bold">Sistem Administrasi Pendidikan Terpadu Lembaga Pendidikan Ma'arif NU PBNU</h6>
                </a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card mx-auto w-75" style="margin-top:-2rem;">
                    <div class="card-body">
                        <h5 class="fw-medium mb-0">Registrasi Satpen</h5>
                        <small>lengkapi kolom untuk registrasi satpen anda</small>
                        <div class="mt-3">
                            @include('template.alert')
                        </div>
                        <form class="mt-3" action="{{ route('register.proses') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="npsn" class="form-label">NPSN</label>
                                        <input type="text" class="form-control form-control-sm @error('npsn') is-invalid @enderror" id="npsn" name="npsn" value="{{ $cookieValue->npsn }}" readonly>
                                        <div class="invalid-feedback">
                                            @error('npsn') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="nm_satpen" class="form-label">Nama Satpen</label>
                                        <input type="text" class="form-control form-control-sm @error('nm_satpen') is-invalid @enderror" id="nm_satpen" name="nm_satpen" value="{{ $cookieValue->nama }}">
                                        <div class="invalid-feedback">
                                            @error('nm_satpen') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="yayasan" class="form-label">Yayasan</label>
                                        <select class="form-select form-select-sm @error('yayasan') is-invalid @enderror" id="yayasan" name="yayasan">
                                            <option value="BHPNU">BHPNU</option>
                                            <option value="non bhpnu">Non BHPNU</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('yayasan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="jenjang" class="form-label">Jenjang Pendidikan</label>
                                        <select class="selectpicker @error('jenjang') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="jenjang">
                                            @foreach($jenjang as $row)
                                                <option value="{{ $row->id_jenjang }}"  {{ strtolower($row->nm_jenjang) == strtolower($cookieValue->bentuk_pendidikan) ? 'selected' : '' }}>{{ $row->nm_jenjang }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('jenjang') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-nm-yayasan">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="nm_yayasan" class="form-label">Nama Yayasan</label>
                                        <input type="text" class="form-control form-control-sm @error('nm_yayasan') is-invalid @enderror" id="nm_yayasan" name="nm_yayasan">
                                        <div class="invalid-feedback">
                                            @error('nm_yayasan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2 d-flex flex-column">
                                        <label for="propinsi" class="form-label">Propinsi</label>
                                        <select class="selectpicker @error('propinsi') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="propinsi">
                                            @foreach($propinsi as $row)
                                                <option value="{{ $row->id_prov }}" {{ strtolower($row->nm_prov) == Strings::removeFirstWord($cookieValue->propinsiluar_negeri_ln) ? 'selected' : '' }}>{{ $row->nm_prov }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('propinsi') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="kabupaten" class="form-label">Kabupaten</label>
                                        <select class="selectpicker @error('kabupaten') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="kabupaten">
                                            @foreach($kabupaten as $row)
                                                <option value="{{ $row->id_kab }}" {{ Strings::removeFirstWord($row->nama_kab) == Strings::removeFirstWord($cookieValue->kabkotanegara_ln) ? 'selected' : '' }}>{{ $row->nama_kab }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('kabupaten') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="cabang" class="form-label">Cabang</label>
                                        <select class="selectpicker @error('cabang') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="cabang">
                                            @foreach($cabang as $row)
                                                <option value="{{ $row->id_pc }}">{{ $row->nama_pc }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('cabang') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" class="form-control form-control-sm @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" value="{{ $cookieValue->kecamatankota_ln }}">
                                        <div class="invalid-feedback">
                                            @error('kecamatan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                        <input type="text" class="form-control form-control-sm @error('kelurahan') is-invalid @enderror" id="kelurahan" name="kelurahan" value="{{ $cookieValue->desakelurahan }}">
                                        <div class="invalid-feedback">
                                            @error('kelurahan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="thn_berdiri" class="form-label">Tahun Berdiri</label>
                                        <input type="text" class="form-control form-control-sm @error('thn_berdiri') is-invalid @enderror" id="thn_berdiri" name="thn_berdiri" value="{{ old('thn_berdiri') }}">
                                        <div class="invalid-feedback">
                                            @error('thn_berdiri') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <input type="text" class="form-control form-control-sm @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ $cookieValue->alamat }}">
                                        <div class="invalid-feedback">
                                            @error('alamat') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="kepsek" class="form-label">Kepala Sekolah</label>
                                        <input type="text" class="form-control form-control-sm @error('kepsek') is-invalid @enderror" id="kepsek" name="kepsek" value="{{ old('kepsek') }}">
                                        <div class="invalid-feedback">
                                            @error('kepsek') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                                        <div class="invalid-feedback">
                                            @error('email') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="telp" class="form-label">Telpon</label>
                                        <input type="text" class="form-control form-control-sm @error('telp') is-invalid @enderror" id="telp" name="telp" value="{{ old('telp') }}">
                                        <div class="invalid-feedback">
                                            @error('telp') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="fax" class="form-label">Fax</label>
                                        <input type="text" class="form-control form-control-sm @error('fax') is-invalid @enderror" id="fax" name="fax" value="{{ old('fax') }}">
                                        <div class="invalid-feedback">
                                            @error('fax') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="aset_tanah" class="form-label">Aset Tanah</label>
                                        <select class="form-select form-select-sm @error('aset_tanah') is-invalid @enderror" name="aset_tanah">
                                            <option value="jamiyah">Jamiyah</option>
                                            <option value="masyarakat nu">Masyarakat NU</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('aset_tanah') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="nm_pemilik" class="form-label">Nama Pemilik</label>
                                        <input type="text" class="form-control form-control-sm @error('nm_pemilik') is-invalid @enderror" id="nm_pemilik" name="nm_pemilik" value="{{ old('aset_tanah') }}">
                                        <div class="invalid-feedback">
                                            @error('nm_pemilik') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="password" class="form-label">Password Akun</label>
                                        <div class="input-group form-password">
                                            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password">
                                            <span class="input-group-text password-toggle">
                                               <i class="ti ti-eye-off"></i>
                                            </span>
                                            <div class="invalid-feedback">
                                                @error('password') {{ $message }} @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="passconfirm" class="form-label">Konfirmasi Password</label>
                                        <div class="input-group form-password">
                                            <input type="password" class="form-control form-control-sm @error('passconfirm') is-invalid @enderror" id="passconfirm" name="passconfirm">
                                            <span class="input-group-text password-toggle">
                                               <i class="ti ti-eye-off"></i>
                                            </span>
                                            <div class="invalid-feedback">
                                                @error('passconfirm') {{ $message }} @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="my-3">Surat Permohonan</h5>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="no_srt_permohonan" class="form-label">Nomor Surat</label>
                                        <input type="text" class="form-control form-control-sm @error('no_srt_permohonan') is-invalid @enderror" id="no_srt_permohonan" name="no_srt_permohonan" value="{{ old('no_srt_permohonan') }}">
                                        <div class="invalid-feedback">
                                            @error('no_srt_permohonan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="tgl_srt_permohonan" class="form-label">Tanggal Surat</label>
                                        <input type="date" class="form-control form-control-sm @error('tgl_srt_permohonan') is-invalid @enderror" id="tgl_srt_permohonan" name="tgl_srt_permohonan">
                                        <div class="invalid-feedback">
                                            @error('tgl_srt_permohonan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="file_permohonan" class="form-label">File Permohonan</label>
                                        <input type="file" class="form-control form-control-sm @error('file_permohonan') is-invalid @enderror" id="file_permohonan" name="file_permohonan" value="{{ old('file_permohonan') }}">
                                        <div class="invalid-feedback">
                                            @error('file_permohonan') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="my-3">Rekomendasi Cabang</h5>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="nm_rekom_pc" class="form-label">Pemberi Rekomendasi</label>
                                        <select class="form-select form-select-sm @error('nm_rekom_pc') is-invalid @enderror" id="nm_rekom_pc" name="nm_rekom_pc">
                                            <option value="PCNU">PCNU</option>
                                            <option value="LP Ma'arif NU PCNU">LP Ma'arif NU PCNU</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('nm_rekom_pc') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="cabang_rekom_pc" class="form-label">Nama Cabang</label>
                                    <select class="selectpicker @error('cabang_rekom_pc') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="cabang_rekom_pc">
                                        @foreach($cabang as $row)
                                            <option value="{{ $row->nama_pc }}">{{ $row->nama_pc }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('cabang_rekom_pc') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="no_srt_rekom_pc" class="form-label">Nomor Surat</label>
                                        <input type="text" class="form-control form-control-sm @error('no_srt_rekom_pc') is-invalid @enderror" id="no_srt_rekom_pc" name="no_srt_rekom_pc" value="{{ old('no_srt_rekom_pc') }}">
                                        <div class="invalid-feedback">
                                            @error('no_srt_rekom_pc') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="tgl_srt_rekom_pc" class="form-label">Tanggal Surat</label>
                                        <input type="date" class="form-control form-control-sm @error('tgl_srt_rekom_pc') is-invalid @enderror" id="tgl_srt_rekom_pc" name="tgl_srt_rekom_pc">
                                        <div class="invalid-feedback">
                                            @error('tgl_srt_rekom_pc') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="file_rekom_pc" class="form-label">File Rekomendasi PC</label>
                                        <input type="file" class="form-control form-control-sm @error('file_rekom_pc') is-invalid @enderror" id="file_rekom_pc" name="file_rekom_pc">
                                        <div class="invalid-feedback">
                                            @error('file_rekom_pc') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 class="my-3">Rekomendasi Wilayah</h5>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="nm_rekom_pw" class="form-label">Pemberi Rekomendasi</label>
                                        <select class="form-select form-select-sm @error('nm_rekom_pw') is-invalid @enderror" id="nm_rekom_pw" name="nm_rekom_pw">
                                            <option value="PWNU">PWNU</option>
                                            <option value="LP Ma'arif NU PWNU">LP Ma'arif NU PWNU</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('nm_rekom_pw') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="wilayah_rekom_pw" class="form-label">Nama Wilayah</label>
                                    <select class="selectpicker @error('wilayah_rekom_pw') is-invalid @enderror" data-show-subtext="false" data-live-search="true" name="wilayah_rekom_pw">
                                        @foreach($propinsi as $row)
                                            <option value="{{ $row->nm_prov }}" {{ strtolower($row->nm_prov) == Strings::removeFirstWord($cookieValue->propinsiluar_negeri_ln) ? 'selected' : '' }}>{{ $row->nm_prov }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('wilayah_rekom_pw') {{ $message }} @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="no_srt_rekom_pw" class="form-label">Nomor Surat</label>
                                        <input type="text" class="form-control form-control-sm @error('no_srt_rekom_pw') is-invalid @enderror" id="no_srt_rekom_pw" name="no_srt_rekom_pw" value="{{ old('no_srt_rekom_pw') }}">
                                        <div class="invalid-feedback">
                                            @error('no_srt_rekom_pw') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-2">
                                        <label for="tgl_srt_rekom_pw" class="form-label">Tanggal Surat</label>
                                        <input type="date" class="form-control form-control-sm @error('tgl_srt_rekom_pw') is-invalid @enderror" id="tgl_srt_rekom_pw" name="tgl_srt_rekom_pw">
                                        <div class="invalid-feedback">
                                            @error('tgl_srt_rekom_pw') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="file_rekom_pw" class="form-label">File Rekomendasi PW</label>
                                        <input type="file" class="form-control form-control-sm @error('file_rekom_pw') is-invalid @enderror" id="file_rekom_pw" name="file_rekom_pw">
                                        <div class="invalid-feedback">
                                            @error('file_rekom_pw') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary fs-4 rounded-2">Daftar</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="container-fluid login-side-right mt-4">
        <div class="row justify-content-center">
            <div class="col-sm-10">
                <div class="py-6 px-6">
                    <p class="mb-0 fs-4 py-3"> Copyright &copy; 2023 Sistem Administrasi Pendidikan Terpadu LP Ma'arif NU PBNU </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        $(".row-nm-yayasan").hide();
        $("#yayasan").on('change', function(e) {
           if ($(this).val() !== "bhpnu") {
               $(".row-nm-yayasan").slideDown()
           } else {
               $(".row-nm-yayasan").slideUp();
           }
        });

        $(".password-toggle").click(function() {
            var passwordField = $(this).parent().find("input");
            var toggleIcon = $(this).find("i");

            if (passwordField.attr("type") === "password") {
                passwordField.attr("type", "text");
                toggleIcon.removeClass("ti-eye-off").addClass("ti-eye");
            } else {
                passwordField.attr("type", "password");
                toggleIcon.removeClass("ti-eye").addClass("ti-eye-off");
            }
        });
    </script>
@endsection
