@extends('layouts.pemilik')

@section('title', 'Dashboard Pemilik')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold" style="color:#243642;">Dashboard Pemilik</h3>
        <p class="text-muted">Ringkasan performa dan laporan pendapatan bisnis Glamping Danau Kembar.</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-4 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-calendar-check fs-4" style="color:#387478;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Total Pemesanan</h6>
                    <small class="text-muted">Semua status</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0" style="color:#387478;">{{ $totalPemesanan }}</h2>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-check-circle fs-4" style="color:#629584;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Pemesanan Selesai</h6>
                    <small class="text-muted">Telah dikonfirmasi</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0" style="color:#629584;">{{ $pemesananSelesai }}</h2>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card border rounded-4 shadow-sm p-4 h-100" style="border-color:#e9ecef!important;">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-3" style="width:50px;height:50px;background:#E2F1E7;">
                    <i class="bi bi-cash-stack fs-4" style="color:#243642;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Total Pendapatan</h6>
                    <small class="text-muted">Telah diverifikasi</small>
                </div>
            </div>
            <h2 class="fw-bold mb-0 fs-3" style="color:#243642;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color:#243642;">Pemesanan Terbaru</h5>
                <a href="{{ route('pemilik.laporan.pemesanan') }}" class="btn btn-sm text-white rounded-pill px-3" style="background:#387478;">Lihat Laporan Lengkap</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Pengunjung</th>
                                <th>Glamping</th>
                                <th>Tanggal Check-in</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemesananTerbaru as $p)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $p->id_pemesanan }}</td>
                                    <td>{{ $p->user->nama ?? '-' }}</td>
                                    <td>{{ $p->glamping->nama_glamping ?? '-' }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tanggal_checkin)) }}</td>
                                    <td class="fw-bold" style="color:#387478;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-secondary">{{ $p->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data pemesanan.</td>
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
