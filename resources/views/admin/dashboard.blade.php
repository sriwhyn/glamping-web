@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold" style="color:#243642;">Dashboard Admin</h3>
        <p class="text-muted">Ringkasan aktivitas dan statistik sistem glamping.</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-3 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-house-door fs-4" style="color:#387478;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Unit Glamping</h6>
                    <small class="text-muted">Total unit</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0" style="color:#387478;">{{ $totalGlamping }}</h2>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-calendar-check fs-4" style="color:#629584;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Total Pemesanan</h6>
                    <small class="text-muted">Semua transaksi</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0" style="color:#629584;">{{ $totalPemesanan }}</h2>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-people fs-4" style="color:#243642;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Pengunjung</h6>
                    <small class="text-muted">User terdaftar</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0" style="color:#243642;">{{ $totalPengunjung }}</h2>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-cash-stack fs-4" style="color:#387478;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Total Pendapatan</h6>
                    <small class="text-muted">Terverifikasi</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0 fs-4" style="color:#387478;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
            <div class="card-header bg-white rounded-top-4 border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color:#243642;">Pemesanan Terbaru</h5>
                <a href="{{ route('admin.pemesanan.index') }}" class="btn btn-sm text-white rounded-pill px-3" style="background:#387478;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Pengunjung</th>
                                <th>Glamping</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemesananTerbaru as $p)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $p->id_pemesanan }}</td>
                                    <td>{{ $p->user->nama ?? '-' }}</td>
                                    <td>{{ $p->glamping->nama_glamping ?? '-' }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tanggal_checkin)) }} - {{ date('d/m/Y', strtotime($p->tanggal_checkout)) }}</td>
                                    <td class="fw-bold">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $p->status }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('admin.pemesanan.show', $p->id_pemesanan) }}" class="btn btn-sm btn-outline-dark rounded-circle"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pemesanan terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection