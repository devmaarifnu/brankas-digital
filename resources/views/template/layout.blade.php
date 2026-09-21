<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  <link rel="shortcut icon" type="image/jpeg" href="{{ asset('assets/images/logos/fotobrankas.jpeg') }}?v=3" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <link rel="stylesheet" href="{{asset('assets/libs/datatables/dataTables.bootstrap5.min.css')}}" />
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ time() }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/modern-table.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/libs/select2/select2.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/libs/select2/select2-bootstrap-5-theme.min.css') }}" />
    <style>
        body {
            background: url("/assets/images/backgrounds/layout_bg.jpg") no-repeat #F5F5F5;
        }
    </style>
  @yield('style')
  @yield('modal-style')
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between" style="height:auto;min-height:82px;padding:10px 18px;border-bottom:1px solid #e5eaef;">
          <a href="{{ route('handover.index') }}" class="text-nowrap logo-img d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logos/fotobrankas-removebg.png') }}?v=4" alt="Brangkas Digital" style="height:62px;max-height:62px;width:auto;object-fit:contain;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.08));" />
            <div class="d-flex flex-column">
              <span class="fw-bolder text-dark lh-1" style="font-size:14px;letter-spacing:0.3px;">BRANGKAS DIGITAL</span>
              <span class="lh-1 mt-1" style="font-size:11px; color:#00713D; letter-spacing:0.3px; font-weight:900;">LP MA'ARIF NU PBNU</span>
            </div>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        @yield('navbar')
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="text-end me-2">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px; letter-spacing: 0.2px;">Brangkas Digital LP Ma'arif NU</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-1 px-2 fs-1 text-uppercase fw-semibold">{{ auth()->user()->name ?: auth()->user()->username }} &bull; {{ auth()->user()->role }}</span>
                </li>
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item" data-bs-toggle="modal" data-bs-target="#modalChangePasswordBackdrop">
                      <i class="ti ti-key fs-6"></i>
                      <p class="mb-0 fs-3">Ganti Password</p>
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-outline-primary mx-3 mt-3 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="container-fluid">

        @yield('container')

        @yield('modals')

      <!-- Modal Change Password -->
      <div class="modal fade" id="modalChangePasswordBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content rounded-2">
                  <div class="modal-header">
                      <div>
                          <h5 class="modal-title mb-0" id="exampleModalLabel">Ganti Password</h5>
                          <small>ganti password lama anda</small>
                      </div>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('changepass') }}" method="post">
                      <div class="modal-body">
                          @csrf
                          <div class="mb-2">
                              <label for="last_pass" class="form-label">Password Lama</label>
                              <div class="input-group form-password">
                                  <input type="password" class="form-control form-control-sm @error('last_pass') is-invalid @enderror" id="nama_prov" name="last_pass" value="{{ old('last_pass') }}" required>
                                  <span class="input-group-text password-toggle">
                                      <i class="ti ti-eye-off"></i>
                                  </span>
                                  <div class="invalid-feedback">
                                      @error('last_pass') {{ $message }} @enderror
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-6">
                                  <label for="new_pass" class="form-label">Password Baru</label>
                                  <div class="input-group form-password">
                                      <input type="password" class="form-control form-control-sm @error('new_pass') is-invalid @enderror" id="new_pass" name="new_pass" value="{{ old('new_pass') }}" required>
                                      <span class="input-group-text password-toggle">
                                         <i class="ti ti-eye-off"></i>
                                      </span>
                                      <div class="invalid-feedback">
                                          @error('new_pass') {{ $message }} @enderror
                                      </div>
                                  </div>
                              </div>
                              <div class="col-sm-6">
                                  <label for="confirm_pass" class="form-label">Konfimasi Password</label>
                                  <div class="input-group form-password">
                                      <input type="password" class="form-control form-control-sm @error('confirm_pass') is-invalid @enderror" id="confirm_pass" name="confirm_pass" value="{{ old('confirm_pass') }}" required>
                                      <span class="input-group-text password-toggle">
                                          <i class="ti ti-eye-off"></i>
                                      </span>
                                      <div class="invalid-feedback" id="password-match-message">
                                          @error('confirm_pass') {{ $message }} @enderror
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-success btn-sm" disabled>Ganti Password</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <!-- End Modal Change Password -->

        <div class="py-3 px-3 text-center mt-2">
          <p class="mb-0 text-muted" style="font-size: 11px; letter-spacing: 0.2px;"> Copyright &copy; {{ date('Y') }} <strong class="text-dark">Brangkas Digital</strong> &mdash; LP Ma'arif NU PBNU &bull; brankas.maarifnu.or.id </p>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <script>
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

      $('#confirm_pass').on('keyup', function () {
            let password = $('#new_pass').val();
            let confirmPassword = $(this).val();
            let message = $('#password-match-message');
            let buttonSubmit = $('#modalChangePasswordBackdrop button[type="submit"]');

            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    message.text("Password tidak cocok!");
                    message.show();
                    buttonSubmit.prop('disabled', true);
                } else {
                    message.text("");
                    message.hide();
                    buttonSubmit.prop('disabled', false);
                }
            } else {
                message.text("");
                message.hide();
            }
        });
  </script>

  <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
  @yield('scripts')
  @yield('modalscripts')
  @yield('extendscripts')
  @stack('scripts')

</body>

</html>
