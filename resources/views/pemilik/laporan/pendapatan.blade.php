@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.pemilik')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Laporan Pendapatan</h3>
        <p class="text-muted mb-0">Rincian pendapatan terverifikasi dari penyewaan glamping.</p>
    </div>
</div>

<div class="card border rounded-4 shadow-sm mb-4" style="border-color:#e9ecef!important;">
    <div class="card-body p-4">
        <form action="{{ route('pemilik.laporan.pendapatan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Check-in Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control rounded-3" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Check-in Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control rounded-3" value="{{ request('tgl_selesai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status Pelunasan</label>
                <select name="status_pelunasan" class="form-select rounded-3">
                    <option value="">Semua Pelunasan</option>
                    <option value="Lunas" {{ request('status_pelunasan') == 'Lunas' ? 'selected' : '' }}>Lunas (100%)</option>
                    <option value="Belum Lunas" {{ request('status_pelunasan') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas (DP 50%)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn text-white rounded-pill px-4 flex-fill" style="background:#387478;">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
                <a href="{{ route('pemilik.laporan.cetak_pendapatan', request()->all()) }}" target="_blank" class="btn btn-dark rounded-pill px-4 flex-fill" style="background:#243642;">
                    <i class="bi bi-printer me-1"></i>Cetak PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards Pendapatan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-start border-4 border-primary">
            <small class="text-muted fw-semibold">Total DP (50%) Diterima</small>
            <h5 class="fw-bold text-primary mb-0 mt-1">Rp {{ number_format($totalDP ?? 0, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-start border-4 border-success">
            <small class="text-muted fw-semibold">Total Pelunasan Diterima</small>
            <h5 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format($totalPelunasan ?? 0, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-start border-4 border-danger">
            <small class="text-muted fw-semibold">Total Denda Kerusakan</small>
            <h5 class="fw-bold text-danger mb-0 mt-1">Rp {{ number_format($totalDendaKerusakan ?? 0, 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm p-3 bg-white border-start border-4" style="border-color:#387478!important;background:#E2F1E7;">
            <small class="text-muted fw-semibold" style="color:#243642;">Total Keseluruhan Pendapatan</small>
            <h4 class="fw-bold mb-0 mt-1" style="color:#387478;">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

<div class="card border rounded-4 shadow-sm" style="border-color:#e9ecef!important;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>ID Pemesanan</th>
                        <th>Pengunjung & Unit</th>
                        <th>Total Tagihan</th>
                        <th>DP Diterima (50%)</th>
                        <th>Pelunasan (50%)</th>
                        <th>Denda Kerusakan</th>
                        <th>Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemesanans as $index => $p)
                        @php
                            $dp = $p->pembayaran ? (float) $p->pembayaran->jumlah_bayar : ($p->total_harga * 0.5);
                            $pelunasan = ($p->status_pelunasan == 'Lunas') ? max(0, $p->total_harga - $dp) : 0;
                            $denda = $p->denda_kerusakan ?? 0;
                            $totalPemasukanPesanan = $dp + $pelunasan + $denda;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td class="fw-bold">#{{ $p->id_pemesanan }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $p->user->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $p->glamping->nama_glamping ?? '-' }}</small>
                            </td>
                            <td class="fw-semibold">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            <td class="text-success font-monospace">Rp {{ number_format($dp, 0, ',', '.') }}</td>
                            <td>
                                @if($p->status_pelunasan == 'Lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">Lunas (+Rp {{ number_format($pelunasan, 0, ',', '.') }})</span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if($denda > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">+Rp {{ number_format($denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="fw-bold fs-6" style="color:#387478;">Rp {{ number_format($totalPemasukanPesanan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data pendapatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
