@extends("template.layout")
@section("navbar") @include("template.nav") @endsection
@section("container")
<div class="container-fluid">
    <nav class="mt-2 mb-3" aria-label="breadcrumb">
        <ul id="breadcrumb" class="mb-0">
            <li><a href="{{ route('handover.index') }}"><i class="ti ti-home"></i></a></li>
            <li><a href="javascript:void(0)">Setting</a></li>
            <li><a href="javascript:void(0)">Users</a></li>
        </ul>
    </nav>

    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(93, 135, 255, 0.1); width: 46px; height: 46px; min-width: 46px;">
                    <i class="ti ti-users fs-5" style="color: #5D87FF;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">Manajemen Users</h4>
                    <small class="text-muted">Kelola akun pengguna dan hak akses sistem Brangkas Digital</small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-semibold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                    <i class="ti ti-user-plus fs-5"></i>
                    <span>Tambah User Baru</span>
                </button>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session("error"))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="ti ti-alert-circle me-2 text-danger fs-5"></i>{{ session("error") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter & Search Card --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="border: 1px solid #ebf1f6;">
        <div class="card-body p-3">
            <form action="{{ route('setting.users') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="role">
                        <option value="">-- Semua Role --</option>
                        <option value="super admin" {{ request('role') === 'super admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="viewer" {{ request('role') === 'viewer' ? 'selected' : '' }}>Viewer</option>
                        <option value="aproval" {{ request('role') === 'aproval' ? 'selected' : '' }}>Aproval</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="status_active">
                        <option value="">-- Semua Status --</option>
                        <option value="active" {{ request('status_active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="block" {{ request('status_active') === 'block' ? 'selected' : '' }}>Nonaktif / Block</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('setting.users') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="ti ti-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Users --}}
    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-list" style="color: #5D87FF;"></i>
                <span>Daftar Pengguna</span>
            </h5>
            <span class="badge bg-light text-primary border">{{ $users->total() }} User Terdaftar</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tblUsers">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #ebf1f6;">
                        <tr>
                            <th class="ps-4" width="40">#</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center pe-4" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr>
                            <td class="ps-4">{{ $users->firstItem() + $i }}</td>
                            <td class="fw-semibold text-dark">{{ $user->name }}</td>
                            <td><code>{{ $user->username }}</code></td>
                            <td>{{ $user->email ?? "-" }}</td>
                            <td>
                                @php
                                    $r = strtolower($user->role);
                                @endphp
                                @if($r === 'super admin')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fw-semibold"><i class="ti ti-shield-lock me-1"></i>Super Admin</span>
                                @elseif($r === 'admin')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-semibold"><i class="ti ti-user-check me-1"></i>Admin</span>
                                @elseif($r === 'viewer')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 fw-semibold"><i class="ti ti-eye me-1"></i>Viewer</span>
                                @elseif($r === 'aproval' || $r === 'approval')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fw-semibold"><i class="ti ti-checklist me-1"></i>Aproval</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status_active == 'active' || $user->status_active == '1' || $user->status_active === true)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="ti ti-check me-1"></i>Aktif</span>
                                @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="ti ti-x me-1"></i>Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex items-center justify-content-center gap-1">
                                    {{-- Edit User Modal Trigger --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id_user ?? $user->id }}" title="Edit User & Password">
                                        <i class="ti ti-pencil"></i>
                                    </button>

                                    {{-- Toggle Status --}}
                                    <form action="{{ route("setting.users.toggle", $user->id_user ?? $user->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $user->status_active == 'active' || $user->status_active == '1' ? 'btn-outline-secondary' : 'btn-outline-success' }} py-1 px-2 shadow-sm" title="{{ $user->status_active == 'active' || $user->status_active == '1' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="ti {{ $user->status_active == 'active' || $user->status_active == '1' ? 'ti-user-x' : 'ti-user-check' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Hapus User --}}
                                    @if(($user->id_user ?? $user->id) != auth()->id())
                                    <form action="{{ route('setting.users.destroy', $user->id_user ?? $user->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 shadow-sm" title="Hapus User">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                {{-- Modal Edit User --}}
                                <div class="modal fade text-start" id="modalEdit{{ $user->id_user ?? $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header py-3 px-4 bg-light border-bottom">
                                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                    <i class="ti ti-user-edit text-primary"></i>
                                                    <span>Edit Data User</span>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('setting.users.update', $user->id_user ?? $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Email</label>
                                                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" placeholder="opsional@email.com">
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="role" required>
                                                                <option value="super admin" {{ $r === 'super admin' ? 'selected' : '' }}>Super admin</option>
                                                                <option value="admin" {{ $r === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                <option value="viewer" {{ $r === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                                                <option value="aproval" {{ $r === 'aproval' || $r === 'approval' ? 'selected' : '' }}>Aproval</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="status_active" required>
                                                                <option value="active" {{ $user->status_active === 'active' || $user->status_active == '1' ? 'selected' : '' }}>Aktif</option>
                                                                <option value="block" {{ $user->status_active === 'block' || $user->status_active == '0' ? 'selected' : '' }}>Nonaktif / Block</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold">Ganti Password <small class="text-muted fw-normal">(Kosongkan jika tidak diubah)</small></label>
                                                        <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter">
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-3 px-4 bg-light border-top">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary fw-semibold"><i class="ti ti-device-floppy me-1"></i>Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data pengguna yang sesuai pencarian / filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Paginasi --}}
            @if($users->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} data
                </small>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Tambah User Baru --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3 px-4 bg-white border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalTambahUserLabel">
                    <i class="ti ti-user-plus text-primary"></i>
                    <span>Tambah User Pengguna Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('setting.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Nama lengkap petugas" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" placeholder="Contoh: petugas_brankas" required value="{{ old('username') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="email@maarifnu.or.id (opsional)" value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role Pengguna <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" required>
                                <option value="admin" selected>Admin (Input & Handover)</option>
                                <option value="super admin">Super admin (Akses Penuh)</option>
                                <option value="viewer">Viewer (Read-only)</option>
                                <option value="aproval">Aproval (Keuangan)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Awal <span class="text-danger">*</span></label>
                            <select class="form-select" name="status_active" required>
                                <option value="active" selected>Aktif</option>
                                <option value="block">Nonaktif / Block</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="ti ti-device-floppy me-1"></i>Simpan User Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection