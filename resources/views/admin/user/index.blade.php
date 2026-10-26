@extends('layouts.admin')

@section('title', 'Data Pengunjung & Persetujuan')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Data Pengguna & Persetujuan Akun</h3>
        <p class="text-muted mb-0">Kelola pengguna terdaftar dan persetujuan pendaftaran akun pengunjung baru.</p>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Role</th>
                        <!-- <th>Status Akun</th>
                        <th class="text-center pe-4">Aksi Persetujuan</th> -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td class="fw-bold" style="color:#243642;">{{ $u->nama }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->no_hp }}</td>
                            <td>
                                @if($u->role == 'pemilik')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">Pemilik</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">Pengunjung</span>
                                @endif
                            </td>
                            <!-- <td>
                                @if($u->status_akun == 'disetujui')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Disetujui</span>
                                @elseif($u->status_akun == 'ditolak')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Ditolak</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">Menunggu Persetujuan</span>
                                @endif
                            </td> -->
                            <!-- <td class="text-center pe-4">
                                @if($u->status_akun == 'pending')
                                    <form action="{{ route('admin.user.setujui', $u->id_user) }}" method="POST" class="d-inline" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 me-1">
                                            <i class="bi bi-check-circle me-1"></i>Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.user.tolak', $u->id_user) }}" method="POST" class="d-inline" onsubmit="this.querySelector('button[type=submit]').disabled=true;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                            <i class="bi bi-x-circle me-1"></i>Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Sudah Diproses</span>
                                @endif
                            </td> -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
