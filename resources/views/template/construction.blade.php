@extends('template.layout', [
    'title' => 'Sipinter - Under Construction'
])

@section('navbar')
    @include('template.nav')
@endsection

@section('container')
<!--  Row 1 -->
<div class="row container-begin">
    <div class="col-sm-12">

        <nav class="mt-2 mb-4" aria-label="breadcrumb">
            <ul id="breadcrumb" class="mb-0">
                <li><a href="#"><i class="ti ti-home"></i></a></li>
                <li><a href="#"><span class=" fa fa-info-circle"> </span> Permohonan</a></li>
                <li><a href="#"><span class="fa fa-snowflake-o"></span> Under Construction</a></li>
            </ul>
        </nav>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Page Under Construction</h5>
                <div class="d-flex align-items-center">
                    <img src="{{ asset('assets/images/backgrounds/undraw_maintenance_re_59vn.svg') }}" class="w-50" alt="Under Construction">
                    <div>
                        <h5>Feature is under development phase</h5>
                        <p>Please come back in next time</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
