@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-selectpicker.css') }}" />
@endsection

<select class="selectpicker" data-show-subtext="false" data-live-search="true" name="{{ $name }}" id="{{ @$id }}">
    <option value="">{{ $default }}</option>
    @foreach ($data as $item)
        <option value="{{ $item[$val] }}" {{ $item[$val] == $current ? 'selected' : '' }}>{{ $prefix . $item[$label] }}
        </option>
    @endforeach
</select>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <script>
        $("select[name='wilayah']").on('change', function() {
            const selectedValue = this.value;
            const currentUrl = new URL(window.location.href);
            const params = currentUrl.searchParams;

            // Tambahkan atau update query param
            params.set('wilayah', selectedValue);

            // Buat URL baru dengan parameter yang sudah diperbarui
            currentUrl.search = params.toString();

            // Redirect ke URL baru
            window.location.href = currentUrl.toString();
        });

        $("select[name='provinsi']").on('change', function() {
            getKabupaten();
            getCabang();
        });

        function getKabupaten(provId) {
            provId = provId ? provId : $("select[name='provinsi']").val();
            let routeGetData = "{{ route('api.kabupatenbyprov', ['provId' => ':param']) }}".replace(':param', provId);

            $.ajax({
                url: routeGetData,
                type: "GET",
                dataType: 'json',
                success: function(res) {

                    let $select = $("select[name='kabupaten']");
                    $select.empty();
                    $select.append("<option value=''>KABUPATEN</option>");

                    $.each(res, function(key, value) {
                        $select.append('<option value=' + value.id_kab + '>' + value.nama_kab +
                            '</option>');
                    });

                    let kabParam = location.search.split("&");
                    if (kabParam.length > 1) {
                        kabParam = kabParam[1]?.split("=")[1];
                        $select.val(kabParam);
                    }

                    $select.selectpicker('refresh');
                }
            })
        }

        function getCabang(provId) {
            provId = provId ? provId : $("select[name='provinsi']").val();
            let routeGetData = "{{ route('api.pcbyprov', ['provId' => ':param']) }}".replace(':param', provId);

            $.ajax({
                url: routeGetData,
                type: "GET",
                dataType: 'json',
                success: function(res) {

                    let $select = $("select[name='cabang']");
                    $select.empty();
                    $select.append("<option value=''>CABANG</option>");

                    $.each(res, function(key, value) {
                        $select.append('<option value=' + value.id_pc + '>' + value.nama_pc +
                            '</option>');
                    });

                    let pcParam = location.search.split("&");
                    if (pcParam.length > 1) {
                        pcParam = pcParam[2]?.split("=")[1];
                        $select.val(pcParam);
                    }

                    $select.selectpicker('refresh');
                }
            })
        }

        getKabupaten({{ in_array(auth()->user()->role, ['admin wilayah']) ? auth()->user()->provId : '' }});
        getCabang({{ in_array(auth()->user()->role, ['admin wilayah']) ? auth()->user()->provId : '' }});
    </script>
@endsection
