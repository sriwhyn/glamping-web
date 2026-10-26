@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.pemilik')

@section('title', 'Laporan Pemesanan')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold" style="color:#243642;">Laporan Pemesanan</h3>
        <p class="text-muted mb-0">Filter dan cetak data transaksi pemesanan glamping.</p>
    </div>
</div>

<div class="card border rounded-4 shadow-sm mb-4" style="border-color:#e9ecef!important;">
    <div class="card-body p-4">
        <form action="{{ route('pemilik.laporan.pemesanan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Tanggal Check-in Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control rounded-3" value="{{ request('tgl_mulai') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">Tanggal Check-in Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control rounded-3" value="{{ request('tgl_selesai') }}">
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label fw-semibold">Status Reservasi</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Semua Status</option>
                    <option value="Menunggu Pembayaran" {{ request('status') == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Dikonfirmasi" {{ request('status') == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label fw-semibold">Status Pelunasan</label>
                <select name="status_pelunasan" class="form-select rounded-3">
                    <option value="">Semua Pelunasan</option>
                    <option value="Lunas" {{ request('status_pelunasan') == 'Lunas' ? 'selected' : '' }}>Lunas (100%)</option>
                    <option value="Belum Lunas" {{ request('status_pelunasan') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas (DP 50%)</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn text-white rounded-pill px-3 flex-fill" style="background:#387478;">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
                <a href="{{ route('pemilik.laporan.cetak_pemesanan', request()->all()) }}" target="_blank" class="btn btn-dark rounded-pill px-3 flex-fill" style="background:#243642;">
                    <i class="bi bi-printer me-1"></i>Cetak PDF
                </a>
            </div>
        </form>
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
                        <th>Pengunjung</th>
                        <th>Glamping</th>
                        <th>Check-in / Check-out</th>
                        <th>Total Tagihan</th>
                        <th>Pelunasan</th>
                        <th>Deposit / Denda</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemesanans as $index => $p)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            <td class="fw-bold">#{{ $p->id_pemesanan }}</td>
                            <td>{{ $p->user->nama ?? '-' }}</td>
                            <td>{{ $p->glamping->nama_glamping ?? '-' }}</td>
                            <td>{{ date('d/m/Y', strtotime($p->tanggal_checkin)) }} - {{ date('d/m/Y', strtotime($p->tanggal_checkout)) }}</td>
                            <td class="fw-bold" style="color:#243642;">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @if($p->status_pelunasan == 'Lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">Lunas</span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill">DP (Sisa Rp {{ number_format($p->sisa_bayar, 0, ',', '.') }})</span>
                                @endif
                            </td>
                            <td>
                                @if($p->denda_kerusakan > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 rounded-pill" title="{{ $p->catatan_kerusakan }}">Denda: Rp {{ number_format($p->denda_kerusakan, 0, ',', '.') }}</span>
                                @elseif($p->status_deposit && $p->status_deposit != 'Belum Bayar')
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 rounded-pill">{{ $p->status_deposit }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status == 'Dikonfirmasi' || $p->status == 'Selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">{{ $p->status }}</span>
                                @elseif($p->status == 'Dibatalkan')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">{{ $p->status }}</span>
                                @elseif($p->status == 'Menunggu Verifikasi')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill">{{ $p->status }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">{{ $p->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data pemesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pemesanans->count() > 0)
                    <tfoot class="bg-light fw-bold border-top">
                        <tr>
                            <td colspan="5" class="text-end ps-4">Total Ringkasan:</td>
                            <td style="color:#243642;">Rp {{ number_format($pemesanans->sum('total_harga'), 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($pemesanans->sum('dibayar'), 0, ',', '.') }}</td>
                            <td class="text-danger">Rp {{ number_format($pemesanans->sum('sisa_bayar'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
