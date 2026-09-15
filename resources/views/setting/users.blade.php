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
                    <small class="text-muted">Kelola akun pengguna sistem Brangkas Digital</small>
                </div>
            </div>
        </div>
    </div>

    @if(session("success"))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="ti ti-check me-2 text-success fs-5"></i>{{ session("success") }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3" style="border: 1px solid #ebf1f6;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-users" style="color: #5D87FF;"></i>
                <span>Daftar Users</span>
            </h5>
            <span class="badge bg-light text-primary border">{{ $users->count() }} User</span>
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
                            <th class="text-center pe-4" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $user)
                        <tr>
                            <td class="ps-4">{{ $i+1 }}</td>
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
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex items-center justify-content-center gap-1">
                                    {{-- Ubah Role Modal Trigger --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRole{{ $user->id_user ?? $user->id }}" title="Ubah Role">
                                        <i class="ti ti-user-cog"></i> Role
                                    </button>

                                    <form action="{{ route("setting.users.toggle", $user->id_user ?? $user->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $user->status_active == 'active' || $user->status_active == '1' ? 'btn-outline-danger' : 'btn-outline-success' }} py-1 px-2 shadow-sm" title="{{ $user->status_active == 'active' || $user->status_active == '1' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="ti {{ $user->status_active == 'active' || $user->status_active == '1' ? 'ti-user-x' : 'ti-user-check' }}"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Modal Ubah Role --}}
                                <div class="modal fade text-start" id="modalRole{{ $user->id_user ?? $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header py-2 px-3 bg-light">
                                                <h6 class="modal-title fw-bold">Ubah Role Pengguna</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('setting.users.updateRole', $user->id_user ?? $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-3">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Pengguna:</small>
                                                        <strong class="text-dark">{{ $user->name }}</strong> (<code>{{ $user->username }}</code>)
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold mb-1">Pilih Role Baru:</label>
                                                        <select class="form-select form-select-sm" name="role" required>
                                                            <option value="super admin" {{ $r === 'super admin' ? 'selected' : '' }}>Super admin (Akses Penuh)</option>
                                                            <option value="admin" {{ $r === 'admin' ? 'selected' : '' }}>Admin (Input & Update Handover)</option>
                                                            <option value="viewer" {{ $r === 'viewer' ? 'selected' : '' }}>Viewer (Hanya Lihat Rekap & Histori)</option>
                                                            <option value="aproval" {{ $r === 'aproval' || $r === 'approval' ? 'selected' : '' }}>Aproval (Persetujuan Pengajuan)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2 px-3 bg-light">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Simpan Role</button>
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
                                Belum ada data pengguna.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection